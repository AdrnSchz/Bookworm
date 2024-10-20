<?php

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

class ProfileController
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

        $user = $_SESSION['user'];
        $messages = $this->flash->getMessages();
        $notifications = $messages['notifications'] ?? [];
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        try {
            return $this->twig->render($response, 'profile.twig', [
                'formAction' => $routeParser->urlFor("handle-profile"),
                'formMethod' => "POST",
                'user' => $user,
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
        $user = $_SESSION['user'];
        $username = $data['username'];
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['profilePicture'] ?? null;

        if (!empty($username)) {
            if ($this->userDAO->usernameIsUnique($username)) {
                $user->setUsername($username);
                $this->userDAO->updateUsername($user->email(), $username);
            } else if ($username !== $user->username()) {
                $errors['username'] = 'This username is already taken.';
            }
        }

        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {

            $imageSize = $uploadedFile->getSize();
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $imageDimensions = getimagesize($uploadedFile->getStream()->getMetadata('uri'));
            $imageWidth = $imageDimensions[0];
            $imageHeight = $imageDimensions[1];

            if ($imageSize >= 1000000 || !in_array($extension, ['png', 'jpg', 'gif', 'svg']) || $imageWidth > 400 || $imageHeight > 400) {
                $errors['picture'] = 'Invalid profile picture. Please upload an image less than 1MB, with dimensions 400x400 pixels or less, and in PNG, JPG, GIF, or SVG format.';
            } else {
                $uuid = uniqid('', true);
                $newFileName = $uuid . '.' . $extension;
                $uploadedFile->moveTo("uploads/$newFileName");

                $user->setProfilePicture($newFileName);
                $this->userDAO->updateProfilePicture($user->email(), "$newFileName");
            }
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        try {
            return $this->twig->render($response, 'profile.twig', [
                'formErrors' => $errors,
                'formAction' => $routeParser->urlFor("handle-profile"),
                'formMethod' => "POST",
                'user' => $_SESSION['user']
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }
}