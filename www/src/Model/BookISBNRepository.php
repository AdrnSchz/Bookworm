<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\Entity\Book;

interface BookISBNRepository
{
    public function getBookByIsbn(string $isbn): Book;
}