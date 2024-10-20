<?php

namespace Project\Bookworm\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Flash\Messages;
use Slim\Psr7\Response;

class CheckSessionMiddleware
{
    private Messages $flash;

    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    public function __invoke(Request $request, RequestHandler $next): Response
    {
        $uri = $request->getUri()->getPath();
        $response =  new Response();

        if (empty($_SESSION['user'])) {
            $this->flash->addMessage('notifications', 'You must be logged in to access the page: '. $uri . '.');
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        } else if ($_SESSION['user']->username() === "" && $request->getUri()->getPath() !== '/profile') {
            $this->flash->addMessage('notifications', 'You must set an username to access the page: ' . $uri . '.');
            return $response->withHeader('Location', '/profile')->withStatus(302);
        }

        return $next->handle($request);
    }
}