<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\Entity\Forum;
use Project\Bookworm\Model\Entity\Post;

interface ForumRepository
{
    public function getAllForums(): ?array;
    public function getForum(int $id): ?Forum;
    public function createForum(Forum $forum): ?Forum;
    public function deleteForum(int $id): bool;
    public function getAllPosts(int $forum_id): ?array;
    public function createPost(Post $post): ?Post;
}