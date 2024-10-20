<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\Book;

use Project\Bookworm\Model\BookRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RatingController
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function putRating(Request $request, Response $response, array $args): Response
    {
        $bookId = intval($args['id']);
        $user = $_SESSION['user'];
        $userId = $user->id();
        $data = $request->getParsedBody();

        $this->bookRepository->put('rating', $userId, $bookId, intval($data['rating']), "");

        return $response;
    }

    public function deleteRating(Request $request, Response $response, array $args): Response
    {
        $bookId = intval($args['id']);
        $user = $_SESSION['user'];
        $userId = $user->id();

        $this->bookRepository->delete('rating', $userId, $bookId);

        return $response;
    }
}