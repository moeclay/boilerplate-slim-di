<?php
declare(strict_types=1);

namespace App\Domain\Health;

use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HealthController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return JsonResponder::success($response, [
            'status' => 'ok',
        ]);
    }
}