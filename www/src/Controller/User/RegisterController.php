<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\User;

use DateTime;
use Project\Bookworm\Model\Entity\User;
use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class RegisterController
{
    private Twig $twig;
    private UserRepository $userDAO;

    public function __construct(Twig $twig, UserRepository $userDAO)
    {
        $this->twig = $twig;
        $this->userDAO = $userDAO;
    }

    public function showForm(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        try {
            return $this->twig->render($response, 'register.twig', [
                'formAction' => $routeParser->urlFor("handle-register"),
                'formMethod' => "POST"
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }

    public function handleFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = [];
        if (empty($data['email'])) {
            $errors['email'] = 'The email field is required.';
        } else if (!(filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['email'] = 'The email address is not valid.';
        } else if (!($this->userDAO->getUserByEmail($data['email']) === null)) {
            $errors['email'] = 'The email address is already registered.';
        }

        if (strlen($data['password']) < 6 || !preg_match('/[0-9]/', $data['password'])){
            $errors['password'] = 'The password must contain at least 6 characters and at least one number.';
        } else if ($data['repeatPassword'] !== $data['password']) {
            $errors['password'] = 'Passwords do not match.';
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (empty($errors)) {
            $user = new User(0, $data['email'], $data['password'], "", "",  new DateTime(), new DateTime());

            if ($this->userDAO->save($user)) {
                $_SESSION['user'] = $this->userDAO->getUserByEmail($data['email']);
                return $response->withHeader('Location', '/catalogue')->withStatus(302);
            } else {
                $errors['save'] = 'Error connecting to the database.';
            }
        }

        try {
            return $this->twig->render($response, 'register.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("handle-register"),
                'formMethod' => "POST",
                'user' => $_SESSION['user'] ?? null
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }
}