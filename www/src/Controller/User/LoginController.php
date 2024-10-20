<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\User;

use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class LoginController
{
    private Twig $twig;
    private UserRepository $userDAO;
    private Messages $flash;

    public function __construct(Twig $twig, UserRepository $userDAO, Messages $flash)
    {
        $this->twig = $twig;
        $this->userDAO = $userDAO;
        $this->flash = $flash;
    }

    public function showForm(Request $request, Response $response): Response
    {
        $messages = $this->flash->getMessages();
        $notifications = $messages['notifications'] ?? [];

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        try {
            return $this->twig->render($response, 'login.twig', [
                'formAction' => $routeParser->urlFor("handle-login"),
                'formMethod' => "POST",
                'notifications' => $notifications
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }

    public function handleFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = [];
        if (empty($data['email']) || !(filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['login'] = 'The email address is not valid.';
        }

        if (empty($errors)) {
            $user = $this->userDAO->getUserByEmail($data['email']);

            if ($user && password_verify($data['password'], $user->Password())) {
                $_SESSION['user'] = $user;

                return $response->withHeader('Location', '/catalogue')->withStatus(302);
            } else {
                $errors['login'] = 'The email address or password is incorrect.';
            }
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        try {
            return $this->twig->render($response, 'login.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("handle-login"),
                'formMethod' => "POST",
                'user' => $_SESSION['user'] ?? null
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }
}