<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\Book;

use Project\Bookworm\Model\BookRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class BookDetailsController
{
    private Twig $twig;
    private BookRepository $bookRepository;

    public function __construct(Twig $twig, BookRepository $bookRepository)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
    }

    public function displayBook(Request $request, Response $response, array $args): Response
    {
        $bookId = intval($args['id']);
        $user = $_SESSION['user'];
        $userId = $user->id();

        $book = $this->bookRepository->getBook($bookId);
        $averageRating = $this->bookRepository->getAverageBookRating($bookId);
        $rated = $this->bookRepository->exists('rating', $userId, $bookId);
        $reviewed = $this->bookRepository->exists('review', $userId, $bookId);
        $reviews = $this->bookRepository->getBookReviews($bookId);

        try {
            return $this->twig->render($response, 'book_details.twig', [
                'book' => $book,
                'averageRating' => $averageRating,
                'rated' => $rated,
                'reviewed' => $reviewed,
                'reviews' => $reviews,
                'user' => $_SESSION['user'] ?? null
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }
}