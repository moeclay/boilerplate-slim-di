<?php
declare(strict_types=1);

namespace App\Domain\Auth;

use App\Infrastructure\Mail\MailService;
use App\Infrastructure\Security\ApiKeyGenerator;
use App\Infrastructure\Security\JwtService;
use App\Infrastructure\Security\PasswordHasher;
use App\Infrastructure\Security\TokenGenerator;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class AuthService
{
    public function __construct(
        private PDO $pdo,
        private PasswordHasher $hasher,
        private TokenGenerator $tokenGenerator,
        private ApiKeyGenerator $apiKeyGenerator,
        private JwtService $jwt,
        private MailService $mail,
        private array $settings
    ) {
    }

    public function register(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $password = (string) ($input['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            throw new RuntimeException('Name, email, and password are required');
        }

        if ($this->findUserByEmail($email)) {
            throw new RuntimeException('Email already registered');
        }

        $verifyToken = $this->tokenGenerator->generate();
        $verifyHash = hash('sha256', $verifyToken);
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $verifyExpires = (new DateTimeImmutable('+' . (int) $this->settings['security']['email_verify_ttl'] . ' seconds'))
            ->format('Y-m-d H:i:s');

        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash, email_verified_at, api_key_prefix, api_key_hash, created_at, updated_at)
             VALUES (:name, :email, :password_hash, NULL, NULL, NULL, :created_at, :updated_at)'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $this->hasher->hash($password),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $userId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare(
            'INSERT INTO email_verifications (user_id, token_hash, expires_at, consumed_at, created_at)
             VALUES (:user_id, :token_hash, :expires_at, NULL, :created_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $verifyHash,
            'expires_at' => $verifyExpires,
            'created_at' => $now,
        ]);

        $this->pdo->commit();

        $verifyLink = rtrim((string) $this->settings['app']['url'], '/') . '/auth/verify-email/' . $verifyToken;
        $this->mail->sendVerificationEmail($email, $name, $verifyLink);

        return [
            'message' => 'Registered. Please check your email for verification link.',
        ];
    }

    public function verify(string $token): array
    {
        if ($token === '') {
            throw new RuntimeException('Token is required');
        }

        $tokenHash = hash('sha256', $token);
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'SELECT ev.id AS verification_id, ev.user_id, u.email, u.name, u.email_verified_at
             FROM email_verifications ev
             INNER JOIN users u ON u.id = ev.user_id
             WHERE ev.token_hash = :token_hash
               AND ev.consumed_at IS NULL
               AND ev.expires_at > :now
             LIMIT 1'
        );
        $stmt->execute([
            'token_hash' => $tokenHash,
            'now' => $now,
        ]);

        $row = $stmt->fetch();
        if (!$row) {
            throw new RuntimeException('Invalid or expired verification token');
        }

        $apiKey = $this->apiKeyGenerator->generate();

        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET email_verified_at = :verified_at,
                 api_key_prefix = :api_key_prefix,
                 api_key_hash = :api_key_hash,
                 updated_at = :updated_at
             WHERE id = :user_id'
        );
        $stmt->execute([
            'verified_at' => $now,
            'api_key_prefix' => $apiKey['prefix'],
            'api_key_hash' => $apiKey['hash'],
            'updated_at' => $now,
            'user_id' => $row['user_id'],
        ]);

        $stmt = $this->pdo->prepare(
            'UPDATE email_verifications
             SET consumed_at = :consumed_at
             WHERE id = :verification_id'
        );
        $stmt->execute([
            'consumed_at' => $now,
            'verification_id' => $row['verification_id'],
        ]);

        $this->pdo->commit();

        return [
            'message' => 'Email verified',
            'api_key' => $apiKey['plain'],
        ];
    }

    public function login(array $input): array
    {
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            throw new RuntimeException('Email and password are required');
        }

        $user = $this->findUserByEmail($email);
        if (!$user) {
            throw new RuntimeException('Invalid credentials');
        }

        if (empty($user['email_verified_at'])) {
            throw new RuntimeException('Email not verified');
        }

        if (! $this->hasher->verify($password, $user['password_hash'])) {
            throw new RuntimeException('Invalid credentials');
        }

        $token = $this->jwt->encode([
            'sub' => (int) $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'api_key_prefix' => $user['api_key_prefix'],
        ]);

        return [
            'token' => $token,
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ],
        ];
    }

    public function profile(int $userId): array
    {
        $user = $this->findUserById($userId);
        if (! $user) {
            throw new RuntimeException('User not found');
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'email_verified_at' => $user['email_verified_at'],
            'api_key_prefix' => $user['api_key_prefix'],
            'api_key_hash' => $user['api_key_hash'],
            'created_at' => $user['created_at'],
        ];
    }

    private function findUserByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    private function findUserById(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}