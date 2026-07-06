<?php
declare(strict_types=1);

namespace App\Domain\Contact;

use DateTimeImmutable;
use PDO;
use RuntimeException;

final class ContactService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, name, email, phone, message, created_at, updated_at
             FROM contacts
             WHERE user_id = :user_id
             ORDER BY id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return ['items' => $stmt->fetchAll()];
    }

    public function create(int $userId, array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));
        $message = trim((string) ($input['message'] ?? ''));

        if ($name === '' || $email === '') {
            throw new RuntimeException('Name and email are required');
        }

        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO contacts (user_id, name, email, phone, message, created_at, updated_at)
             VALUES (:user_id, :name, :email, :phone, :message, :created_at, :updated_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $this->show($userId, (int) $this->pdo->lastInsertId());
    }

    public function show(int $userId, int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, name, email, phone, message, created_at, updated_at
             FROM contacts
             WHERE id = :id AND user_id = :user_id
             LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $item = $stmt->fetch();
        if (! $item) {
            throw new RuntimeException('Contact not found');
        }

        return ['item' => $item];
    }

    public function update(int $userId, int $id, array $input): array
    {
        $existing = $this->show($userId, $id);

        $name = trim((string) ($input['name'] ?? $existing['item']['name']));
        $email = trim((string) ($input['email'] ?? $existing['item']['email']));
        $phone = trim((string) ($input['phone'] ?? $existing['item']['phone']));
        $message = trim((string) ($input['message'] ?? $existing['item']['message']));
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'UPDATE contacts
             SET name = :name,
                 email = :email,
                 phone = :phone,
                 message = :message,
                 updated_at = :updated_at
             WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'updated_at' => $now,
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $this->show($userId, $id);
    }

    public function delete(int $userId, int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM contacts WHERE id = :id AND user_id = :user_id');
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Contact not found');
        }
    }
}