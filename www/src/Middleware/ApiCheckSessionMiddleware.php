<?php

namespace Project\Bookworm\Middleware;

use Project\Bookworm\Helpers\JsonEncoder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ApiCheckSessionMiddleware
{
    private JsonEncoder $json;

    public function __construct(JsonEncoder $json)
    {
        $this->json = $json;
    }

    public function __invoke(Request $request, RequestHandler $next): Response
    {
        if (empty($_SESSION['user'])) {
            $err = ['message' => 'This API can only be used by authenticated users.'];
            return $this->json->encode($err)->withStatus(401);
        } else if ($_SESSION['user']->username() === "" && $request->getUri()->getPath() !== '/profile') {
            $err = ['message' => 'This API can only be used by users with a defined username.'];
            return $this->json->encode($err)->withStatus(403);
        }

        return $next->handle($request);
    }
}