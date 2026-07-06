<?php
declare(strict_types=1);

namespace App\Domain\Auth;

use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

final class AuthController
{
    public function __construct(private AuthService $service)
    {
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return JsonResponder::success(
            $response,
            $this->service->register((array) $request->getParsedBody()),
            201
        );
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return JsonResponder::success(
            $response,
            $this->service->login((array) $request->getParsedBody())
        );
    }

    public function verify(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
{
        $route = RouteContext::fromRequest($request)->getRoute();
        $token = $route?->getArgument('token', '');

        return JsonResponder::success(
            $response,
            $this->service->verify((string) $token)
        );
    }

    public function me(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = (array) $request->getAttribute('auth_user', []);
        return JsonResponder::success(
            $response,
            $this->service->profile((int) ($auth['sub'] ?? 0))
        );
    }
}