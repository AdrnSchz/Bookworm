<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\Entity\User;

interface UserRepository
{
    public function save(User $user): bool;
    public function getUserByEmail(string $email): ?User;
    public function getUserById(int $id): ?User;
}