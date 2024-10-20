<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\Entity\Book;

interface BookRepository
{
    public function getAllBookDisplays(): array;

    public function saveBook(Book $book): bool;

    public function getBook(int $id): Book;

    public function getAverageBookRating(int $bookId): string;

    public function getBookReviews(int $bookId): array;

    public function exists(string $action, int $userId, int $bookId): bool;

    public function put(string $action, int $userId, int $bookId, int $rating, string $review): bool;

    public function delete(string $action, int $userId, int $bookId): bool;
}