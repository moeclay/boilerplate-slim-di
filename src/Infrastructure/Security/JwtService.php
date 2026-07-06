<?php
declare(strict_types=1);

namespace App\Infrastructure\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

final class JwtService
{
    public function __construct(private array $settings)
    {
    }

    public function encode(array $claims): string
    {
        $now = time();

        $payload = array_merge([
            'iss' => $this->settings['issuer'],
            'aud' => $this->settings['audience'],
            'iat' => $now,
            'exp' => $now + (int) $this->settings['ttl'],
        ], $claims);

        return JWT::encode($payload, $this->settings['secret'], 'HS256');
    }

    public function decode(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->settings['secret'], 'HS256'));
        return json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }
}