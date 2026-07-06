<?php
declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use App\Error\JsonErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

$root = dirname(__DIR__);

if (is_file($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$settings = require $root . '/config/settings.php';

$builder = new ContainerBuilder();
$builder->addDefinitions([
    'settings' => require $root . '/config/settings.php',
]);
$builder->addDefinitions(require $root . '/config/dependencies.php');
$container = $builder->build();

$app = Bridge::create($container);
$app->addBodyParsingMiddleware();

(require $root . '/config/middleware.php')($app, $container);
(require $root . '/config/routes.php')($app, $container);

$errorMiddleware = $app->addErrorMiddleware($settings['app']['debug'], true, true);
$errorMiddleware->setDefaultErrorHandler(
    new JsonErrorHandler(
        $app->getCallableResolver(),
        $app->getResponseFactory()
    )
);

$app->run();