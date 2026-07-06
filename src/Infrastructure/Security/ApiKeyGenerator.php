<?php
declare(strict_types=1);

namespace App\Infrastructure\Security;

final class ApiKeyGenerator
{
    public function __construct(private string $prefix = 'pk_')
    {
    }

    public function generate(): array
    {
        $plain = $this->prefix . bin2hex(random_bytes(32));

        return [
            'plain' => $plain,
            'hash' => hash('sha256', $plain),
            'prefix' => $this->prefix,
        ];
    }
}