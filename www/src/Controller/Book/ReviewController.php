<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\Book;

use Project\Bookworm\Model\BookRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ReviewController
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function putReview(Request $request, Response $response, array $args): Response
    {
        $bookId = intval($args['id']);
        $user = $_SESSION['user'];
        $userId = $user->id();
        $data = $request->getParsedBody();

        $this->bookRepository->put('review', $userId, $bookId, -1, $data['review']);

        return $response;
    }

    public function deleteReview(Request $request, Response $response, array $args): Response
    {
        $bookId = intval($args['id']);
        $user = $_SESSION['user'];
        $userId = $user->id();

        $this->bookRepository->delete('review', $userId, $bookId);

        return $response;
    }
}