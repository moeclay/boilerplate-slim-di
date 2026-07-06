<?php
declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\ResponseInterface;

final class JsonResponder
{
    public static function respond(ResponseInterface $response, array $payload, int $status = 200): ResponseInterface
    {
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public static function success(ResponseInterface $response, array $data = [], int $status = 200): ResponseInterface
    {
        return self::respond($response, [
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public static function error(ResponseInterface $response, string $message, int $status = 400, array $extra = []): ResponseInterface
    {
        return self::respond($response, array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $status);
    }
}