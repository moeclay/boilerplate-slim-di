<?php
declare(strict_types=1);

use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (is_file(__DIR__ . '/.env')) {
    Dotenv::createImmutable(__DIR__)->safeLoad();
}

$driver = $_ENV['DB_DRIVER'] ?? 'sqlite';

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'default',
        'default' => [
            'adapter' => $driver,
            'name' => $driver === 'mysql'
                ? ($_ENV['DB_DATABASE'] ?? 'app')
                : ((realpath(__DIR__) ?: __DIR__) . DIRECTORY_SEPARATOR . ($_ENV['DB_SQLITE_PATH'] ?? 'storage/sqlite/app.sqlite')),
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'user' => $_ENV['DB_USERNAME'] ?? 'root',
            'pass' => $_ENV['DB_PASSWORD'] ?? '',
            'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
            'charset' => 'utf8mb4',
        ],
    ],
];