<?php
declare(strict_types=1);

use App\Domain\Auth\AuthService;
use App\Domain\Contact\ContactService;
use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Mail\MailService;
use App\Infrastructure\Security\ApiKeyGenerator;
use App\Infrastructure\Security\JwtService;
use App\Infrastructure\Security\PasswordHasher;
use App\Infrastructure\Security\TokenGenerator;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use PDO;

return [
    PDO::class => static function (ContainerInterface $container): PDO {
        return ConnectionFactory::create($container->get('settings')['db']);
    },
    LoggerInterface::class => static function (ContainerInterface $container): LoggerInterface {
        $settings = $container->get('settings');
        $logger = new Logger($settings['app']['name']);
        $logger->pushHandler(
            new StreamHandler(__DIR__ . '/../storage/logs/app.log', Level::Debug)
        );
        return $logger;
    },
    Mailer::class => static function (ContainerInterface $container): Mailer {
        $dsn = $container->get('settings')['mail']['dsn'];
        return new Mailer(Transport::fromDsn($dsn));
    },
    PasswordHasher::class => static fn () => new PasswordHasher(),
    TokenGenerator::class => static fn () => new TokenGenerator(),
    ApiKeyGenerator::class => static function (ContainerInterface $container): ApiKeyGenerator {
        return new ApiKeyGenerator($container->get('settings')['security']['api_key_prefix']);
    },
    JwtService::class => static function (ContainerInterface $container): JwtService {
        return new JwtService($container->get('settings')['jwt']);
    },
    MailService::class => static function (ContainerInterface $container): MailService {
        return new MailService($container->get(Mailer::class), $container->get('settings')['mail']);
    },
    AuthService::class => static fn (ContainerInterface $container) => new AuthService(
        $container->get(PDO::class),
        $container->get(PasswordHasher::class),
        $container->get(TokenGenerator::class),
        $container->get(ApiKeyGenerator::class),
        $container->get(JwtService::class),
        $container->get(MailService::class),
        $container->get('settings')
    ),
    ContactService::class => static fn (ContainerInterface $container) => new ContactService(
        $container->get(PDO::class)
    ),
];