<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;

final class ConnectionFactory
{
    public static function create(array $db): PDO
    {
        if (($db['driver'] ?? 'sqlite') === 'mysql') {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $db['host'],
                $db['port'],
                $db['database']
            );
        } else {
            $dsn = 'sqlite:' . $db['sqlite_path'];
        }

        return new PDO($dsn, $db['username'] ?? null, $db['password'] ?? null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}