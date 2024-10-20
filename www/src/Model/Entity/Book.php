<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Entity;

final class Book
{   
    private int $id;
    private string $title;
    private string $author;
    private string $description;
    private int $numPages;
    private string $url;

    public function __construct(int $id, string $title, string $author, string $description, int $numPages, string $url) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->numPages = $numPages;
        $this->url = $url;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function description(): string
    {
        return $this->description;
    }
    
    public function numPages(): int
    {
        return $this->numPages;
    }

    public function url(): string
    {
        return $this->url;
    }
}