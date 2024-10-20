<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Entity;

use DateTime;

final class User
{
    private int $id;
    private string $email;
    private string $password;
    private string $username;
    private string $profile_picture;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(int $id, string $email, string $password, string $username, string $profile_picture, DateTime $createdAt, DateTime $updatedAt) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->profile_picture = $profile_picture;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function profile_picture(): string
    {
        return $this->profile_picture;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setProfilePicture(string $profile_picture): void
    {
        $this->profile_picture = $profile_picture;
    }
}