<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class HomeController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function apply(Request $request, Response $response): Response
    {
        try {
            return $this->twig->render($response, 'home.twig', [
                'user' => $_SESSION['user'] ?? null
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }
}