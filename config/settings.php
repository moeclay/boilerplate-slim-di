<?php
declare(strict_types=1);

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'PHPDI Boilerplate',
        'env' => $_ENV['APP_ENV'] ?? 'local',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
        'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
    ],
    'db' => [
        'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite',
        'sqlite_path' => $_ENV['DB_SQLITE_PATH'] ?? __DIR__ . '/../storage/sqlite/app.sqlite',
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_DATABASE'] ?? 'app',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ],
    'mail' => [
        'dsn' => $_ENV['MAILER_DSN'] ?? 'smtp://localhost',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.com',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'PHPDI Boilerplate',
    ],
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'change-me',
        'issuer' => $_ENV['JWT_ISSUER'] ?? 'phpdi-boilerplate',
        'audience' => $_ENV['JWT_AUDIENCE'] ?? 'phpdi-boilerplate-users',
        'ttl' => (int) ($_ENV['JWT_TTL'] ?? 3600),
    ],
    'security' => [
        'api_key_prefix' => $_ENV['API_KEY_PREFIX'] ?? 'pk_',
        'email_verify_ttl' => (int) ($_ENV['EMAIL_VERIFY_TTL'] ?? 86400),
    ],
];