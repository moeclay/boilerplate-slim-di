<?php
declare(strict_types=1);

use App\Domain\Auth\AuthController;
use App\Domain\Contact\ContactController;
use App\Domain\Health\HealthController;
use App\Infrastructure\Security\JwtService;
use App\Middleware\JwtMiddleware;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app, ContainerInterface $container): void {
    $app->get('/health', [HealthController::class, 'index']);

    $app->post('/auth/register', [AuthController::class, 'register']);
    $app->post('/auth/login', [AuthController::class, 'login']);
    $app->get('/auth/verify-email/{token}', [AuthController::class, 'verify']);

    $app->group('', function (RouteCollectorProxy $group): void {
        $group->get('/me', [AuthController::class, 'me']);

        $group->get('/contact', [ContactController::class, 'index']);
        $group->post('/contact', [ContactController::class, 'store']);
        $group->get('/contact/{id}', [ContactController::class, 'show']);
        $group->put('/contact/{id}', [ContactController::class, 'update']);
        $group->delete('/contact/{id}', [ContactController::class, 'destroy']);
    })->add(new JwtMiddleware($container->get(JwtService::class)));
};