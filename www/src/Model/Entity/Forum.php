<?php

namespace Project\Bookworm\Model\Entity;

use JsonSerializable;

final class Forum implements JsonSerializable
{
    private int $id;
    private string $title;
    private string $description;

    public function __construct(?int $id, string $title, string $description)
    {
        if ($id) $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}