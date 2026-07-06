<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Infrastructure\Security\JwtService;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;
use Throwable;

final class JwtMiddleware implements MiddlewareInterface
{
    public function __construct(private JwtService $jwt)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');

        if (!str_starts_with($header, 'Bearer ')) {
            return JsonResponder::error(new Response(), 'Unauthorized', 401);
        }

        $token = trim(substr($header, 7));

        try {
            $payload = $this->jwt->decode($token);
        } catch (Throwable) {
            return JsonResponder::error(new Response(), 'Invalid or expired token', 401);
        }

        return $handler->handle($request->withAttribute('auth_user', $payload));
    }
}