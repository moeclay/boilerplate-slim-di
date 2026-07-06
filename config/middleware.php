<?php
declare(strict_types=1);

use App\Middleware\LoggerMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;

return static function (App $app, ContainerInterface $container): void {
    $app->addRoutingMiddleware();
    $app->add(new LoggerMiddleware($container->get(LoggerInterface::class)));
};