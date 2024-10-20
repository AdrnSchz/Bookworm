<?php

namespace Project\Bookworm\Model\Entity;

use JsonSerializable;

final class Post implements JsonSerializable
{
    private int $id;
    private int $forumId;
    private int $userId;
    private string $title;
    private string $contents;
    private string $username;
    private string $profilePicture;

    public function __construct(?int $id, int $forumId, int $userId, string $title, string $contents, ?string $username, ?string $profilePicture)
    {
        if ($id) $this->id = $id;
        $this->forumId = $forumId;
        $this->userId = $userId;
        $this->title = $title;
        $this->contents = $contents;
        if ($username) $this->username = $username;
        if ($profilePicture) $this->profilePicture = $profilePicture;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function forumId(): int
    {
        return $this->forumId;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setUser(User $user)
    {
        $this->username = $user->username();

        if ($user->profile_picture() != "") {
            $this->profilePicture = 'http://localhost:8080/uploads/' . $user->profile_picture();
        }
        else {
            $this->profilePicture = "";
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'contents' => $this->contents,
            'opUsername' => $this->username,
            'opProfilePicture' => $this->profilePicture,
        ];
    }
}