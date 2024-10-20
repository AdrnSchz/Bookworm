<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Entity;

final class BookDisplay
{
    private int $id;
    private string $title;
    private string $url;

    public function __construct(int $id, string $title, string $url) {
        $this->id = $id;
        $this->title = $title;
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

    public function url(): string
    {
        return $this->url;
    }
}