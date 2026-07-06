<?php
declare(strict_types=1);

namespace App\Error;

use Psr\Http\Message\ResponseInterface;
use Slim\Handlers\ErrorHandler;
use Throwable;

final class JsonErrorHandler extends ErrorHandler
{
    protected function respond(): ResponseInterface
    {
        $payload = [
            'success' => false,
            'message' => $this->exception->getMessage(),
        ];

        if ($this->displayErrorDetails) {
            $payload['trace'] = $this->exception->getTrace();
            $payload['type'] = $this->exception::class;
        }

        $response = $this->responseFactory->createResponse($this->statusCode);
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return $response->withHeader('Content-Type', 'application/json');
    }
}