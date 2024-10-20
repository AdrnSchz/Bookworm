<?php

namespace Project\Bookworm\Model\Repository;

use PDO;
use PDOException;
use Project\Bookworm\Model\Entity\Forum;
use Project\Bookworm\Model\Entity\Post;
use Project\Bookworm\Model\ForumRepository;

final class MysqlForumRepository implements ForumRepository
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getAllForums(): ?array
    {
        try {
            $statement = $this->database->prepare("SELECT * FROM forums");
            $statement->execute();

            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($data)) {
                return array_map(function($row) {
                    return new Forum($row['id'], $row['title'], $row['description']);
                }, $data);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getForum(int $id): ?Forum
    {
        try {
            $statement = $this->database->prepare("SELECT * FROM forums WHERE id = :id");
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            $data = $statement->fetch(PDO::FETCH_ASSOC);

            if (!empty($data)) {
                return new Forum($data['id'], $data['title'], $data['description']);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return null;
        }
    }

    public function createForum(Forum $forum): ?Forum
    {
        $title = $forum->title();
        $description = $forum->description();

        $query = "INSERT INTO forums (title, description) VALUES (:title, :description)";

        try {
            $statement = $this->database->prepare($query);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':description', $description);
            $statement->execute();

            $forum->setId($this->database->lastInsertId());
            return $forum;
        } catch(PDOException $e) {
            return null;
        }
    }

    public function deleteForum(int $id): bool
    {
        $query = "DELETE FROM forums WHERE id = :id";

        try {
            $statement = $this->database->prepare($query);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            if ($statement->rowCount() > 0) return true;
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getAllPosts(int $forum_id): ?array
    {
        $query = "SELECT * FROM posts WHERE forum_id = :forum_id";

        try {
            $statement = $this->database->prepare($query);
            $statement->bindParam(':forum_id', $forum_id, PDO::PARAM_INT);
            $statement->execute();

            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($data)) {
                return array_map(function($row) {
                    return new Post($row['id'], $row['forum_id'], $row['user_id'], $row['title'], $row['contents'], null, null);
                }, $data);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return null;
        }
    }

    public function createPost(Post $post): ?Post
    {
        $forumId = $post->forumId();
        $userId = $post->userId();
        $title = $post->title();
        $contents = $post->contents();

        $query = "INSERT INTO posts (forum_id, user_id, title, contents) VALUES (:forum_id, :user_id, :title, :contents)";

        try {
            $statement = $this->database->prepare($query);
            $statement->bindParam(':forum_id', $forumId, PDO::PARAM_INT);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':contents', $contents);
            $statement->execute();

            $post->setId($this->database->lastInsertId());
            return $post;
        } catch(PDOException $e) {
            return null;
        }
    }
}