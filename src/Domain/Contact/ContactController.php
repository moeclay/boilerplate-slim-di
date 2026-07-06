<?php
declare(strict_types=1);

namespace App\Domain\Contact;

use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

final class ContactController
{
    public function __construct(private ContactService $service)
    {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = (array) $request->getAttribute('auth_user', []);
        return JsonResponder::success($response, $this->service->all((int) ($auth['sub'] ?? 0)));
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = (array) $request->getAttribute('auth_user', []);
        return JsonResponder::success(
            $response,
            $this->service->create((int) ($auth['sub'] ?? 0), (array) $request->getParsedBody()),
            201
        );
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = (array) $request->getAttribute('auth_user', []);
        $route = RouteContext::fromRequest($request)->getRoute();
        $id = (int) ($route?->getArgument('id') ?? 0);

        return JsonResponder::success(
            $response,
            $this->service->show((int) ($auth['sub'] ?? 0), $id)
        );
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = (array) $request->getAttribute('auth_user', []);
        $route = RouteContext::fromRequest($request)->getRoute();
        $id = (int) ($route?->getArgument('id') ?? 0);

        return JsonResponder::success(
            $response,
            $this->service->update((int) ($auth['sub'] ?? 0), $id, (array) $request->getParsedBody())
        );
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = (array) $request->getAttribute('auth_user', []);
        $route = RouteContext::fromRequest($request)->getRoute();
        $id = (int) ($route?->getArgument('id') ?? 0);

        $this->service->delete((int) ($auth['sub'] ?? 0), $id);

        return JsonResponder::success($response, ['message' => 'Deleted']);
    }
}