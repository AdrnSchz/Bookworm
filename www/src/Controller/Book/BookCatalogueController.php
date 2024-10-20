<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\Book;

use Project\Bookworm\Model\BookISBNRepository;
use Project\Bookworm\Model\BookRepository;
use Project\Bookworm\Model\Entity\Book;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class BookCatalogueController
{
    private Twig $twig;
    private BookRepository $bookRepository;
    private BookISBNRepository $bookISBNRepository;

    public function __construct(Twig $twig, BookRepository $bookRepository, BookISBNRepository $bookISBNRepository)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
        $this->bookISBNRepository = $bookISBNRepository;
    }

    public function showCatalogue(Request $request, Response $response): Response
    {
        $books = $this->bookRepository->getAllBookDisplays();

        try {
            return $this->twig->render($response, 'book_catalogue.twig', [
                'books' => $books,
                'user' => $_SESSION['user'] ?? null
            ]);
        } catch (LoaderError | RuntimeError| SyntaxError $e) {
            return $response->withStatus(500);
        }
    }

    public function handleFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $saved = false;

        if (isset($_POST['full_form'])) {

            if (!empty($data['title']) && !empty($data['author']) && !empty($data['description']) && !empty($data['num_pages'])){
                $imageUrl = '';
                
                if (!empty($data['url'])) {
                    $imageUrl = $data['url'];
                }
                $book = new Book(-1, $data['title'], $data['author'], $data['description'], intval($data['num_pages']), $imageUrl);
                $this->bookRepository->saveBook($book);
            }  
        } 
        else if (isset($_POST['import_form'])) {

            if (!empty($data['book_isbn'])){

                $book = $this->bookISBNRepository->getBookByISBN($data['book_isbn']);
                if ($book->id() != -1) {
                    $this->bookRepository->saveBook($book);
                }
            }
        }
        
        return $this->showCatalogue($request, $response);
    }
}