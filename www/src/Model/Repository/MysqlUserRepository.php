<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use DateTime;
use Exception;
use PDO;
use PDOException;
use Project\Bookworm\Model\Entity\User;
use Project\Bookworm\Model\UserRepository;

final class MysqlUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function save(User $user): bool
    {
        try {
            $email = $user->email();
            $password_hash = password_hash($user->password(), PASSWORD_DEFAULT);
            $created = $user->createdAt()->format(self::DATE_FORMAT);
            $updated = $user->updatedAt()->format(self::DATE_FORMAT);

            $statement = $this->database->prepare('INSERT INTO users (email, password, created_at, updated_at) VALUES (:email, :password, :created, :updated)');
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $statement->bindParam(':created', $created, PDO::PARAM_STR);
            $statement->bindParam(':updated', $updated, PDO::PARAM_STR);
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getUserByEmail(string $email): ?User
    {
        try {
            $statement = $this->database->prepare("SELECT * FROM users WHERE email = :email");
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            $userData = $statement->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return $this->dataToUser($userData);
            } else {
                return null;
            }
        } catch(PDOException $e) {
            return null;
        }
    }

    public function getUserById(int $id): ?User
    {
        try {
            $statement = $this->database->prepare("SELECT * FROM users WHERE id = :id");
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            $userData = $statement->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return $this->dataToUser($userData);
            } else {
                return null;
            }
        } catch(PDOException $e) {
            return null;
        }
    }

    private function dataToUser(array $data): User {
        $username = $data['username'] ?? '';
        $profile_picture = $data['profile_picture'] ?? '';
        try {
            return new User($data['id'], $data['email'], $data['password'], $username, $profile_picture, new DateTime($data['created_at']), new DateTime($data['updated_at']));
        } catch (Exception $e) {
            return new User($data['id'], $data['email'], $data['password'], $username, $profile_picture, new DateTime("@0"), new DateTime("@0"));
        }
    }

    public function usernameIsUnique(string $username): bool
    {
        try {
            $statement = $this->database->prepare("SELECT * FROM users WHERE username = :username");
            $statement->bindParam(':username', $username, PDO::PARAM_STR);
            $statement->execute();

            $userData = $statement->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return false;
            } else {
                return true;
            }
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateUsername(string $email, string $username): bool
    {
        try {
            $dataTime = new DateTime();
            $updated = $dataTime->format(self::DATE_FORMAT);

            $statement = $this->database->prepare('UPDATE users SET username = :username, updated_at = :updated WHERE email = :email');
            $statement->bindParam(':username', $username, PDO::PARAM_STR);
            $statement->bindParam(':updated', $updated, PDO::PARAM_STR);
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateProfilePicture(string $email, string $profile_picture): bool
    {
        try {
            $dataTime = new DateTime();
            $updated = $dataTime->format(self::DATE_FORMAT);

            $statement = $this->database->prepare('UPDATE users SET profile_picture = :profile_picture, updated_at = :updated WHERE email = :email');
            $statement->bindParam(':profile_picture', $profile_picture, PDO::PARAM_STR);
            $statement->bindParam(':updated', $updated, PDO::PARAM_STR);
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->execute();

            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}