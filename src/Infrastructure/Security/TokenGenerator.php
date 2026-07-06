<?php
declare(strict_types=1);

namespace App\Infrastructure\Security;

final class TokenGenerator
{
    public function generate(int $bytes = 32): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }
}