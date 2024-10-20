<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use Exception;
use PDO;
use PDOException;
use Project\Bookworm\Model\BookRepository;
use Project\Bookworm\Model\Entity\Book;
use Project\Bookworm\Model\Entity\BookDisplay;
use Project\Bookworm\Model\Rating;

final class MysqlBookRepository implements BookRepository
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function getAllBookDisplays(): array
    {   
        $books = [];
        try {
            $query = <<<'QUERY'
            SELECT id, title, cover_image FROM books
            QUERY;
            $statement = $this->database->prepare($query);
            $statement->execute();
            $queryReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if ($queryReturn) {
                foreach ($queryReturn as $bookReturn) {
                    array_push($books, new BookDisplay($bookReturn['id'], $bookReturn['title'], $bookReturn['cover_image']));
                }
            }
        } 
        catch(PDOException $e2) {}

        return $books;
    }

    public function saveBook(Book $book): bool
    {
        try {
            $title = $book->title();
            $author = $book->author();
            $description = $book->description();
            $numPages = $book->numPages();
            $url = $book->url();

            $query = <<<'QUERY'
            INSERT INTO books (title, author, description, page_number, cover_image) VALUES (:title, :author, :description, :page_number, :cover_image)
            QUERY;
            $statement = $this->database->prepare($query);
            $statement->bindParam(':title', $title, PDO::PARAM_STR);
            $statement->bindParam(':author', $author, PDO::PARAM_STR);
            $statement->bindParam(':description', $description, PDO::PARAM_STR);
            $statement->bindParam(':page_number', $numPages, PDO::PARAM_INT);
            $statement->bindParam(':cover_image', $url, PDO::PARAM_STR);
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getBook(int $id): Book
    {
        try {
            $query = <<<'QUERY'
            SELECT * FROM books WHERE id = :book_id
            QUERY;
            $statement = $this->database->prepare($query);
            $statement->bindParam(':book_id', $id, PDO::PARAM_INT);
            $statement->execute();

            $bookReturn = $statement->fetch(PDO::FETCH_ASSOC);
            
            if ($bookReturn) {
                return new Book($id, $bookReturn['title'], $bookReturn['author'], $bookReturn['description'], $bookReturn['page_number'], $bookReturn['cover_image']);
            }
        }
        catch (PDOException $e) {}
        
        return new Book(-1, "", "", "", -1, "");
    }

    public function getBookReviews(int $bookId): array
    {      
        $reviews = [];
        try {
            $query = <<<'QUERY'
            SELECT text FROM reviews WHERE book_id = :book_id
            QUERY;
            $statement = $this->database->prepare($query);
            $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $statement->execute();

            $reviewsReturned = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($reviewsReturned as $reviewReturned) {
                array_push($reviews, $reviewReturned['text']);
            }
        }
        catch (PDOException $e) {}

        return $reviews;
    }

    public function getAverageBookRating(int $bookId): string
    {      
        $average = 0;
        try {
            $query = <<<'QUERY'
            SELECT rating FROM ratings WHERE book_id = :book_id
            QUERY;
            $statement = $this->database->prepare($query);
            $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $statement->execute();

            $ratingsReturned = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if ($ratingsReturned) {
                foreach ($ratingsReturned as $rating) {
                    $average += $rating['rating'];
                }
                $average /= count($ratingsReturned);
                return strval($average);
            }
            else {
                return "this book hasn't been rated yet";
            }
        }
        catch (PDOException $e) {}

        return "";
    }

    public function exists(string $action, int $userId, int $bookId): bool
    {
        try {
            $column = 'rating';
            if ($action === 'review') {
                $column = 'text';
            }
            $query = 'SELECT '.$column.' FROM '.$action.'s WHERE user_id = :user_id AND book_id = :book_id';

            $statement = $this->database->prepare($query);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $statement->execute();

            $ratingReturned = $statement->fetch(PDO::FETCH_ASSOC);
            
            if ($ratingReturned) {
                return true;
            }
        }
        catch (PDOException $e) {}

        return false;
    }

    public function put(string $action, int $userId, int $bookId, int $rating, string $review): bool
    {   
        if ($this->exists($action, $userId, $bookId)) {
            return $this->update($action, $userId, $bookId, $rating, $review);
        }
        else {
            return $this->post($action, $userId, $bookId,  $rating, $review);
        }
    }

    private function update(string $action, int $userId, int $bookId, int $rating, string $review): bool
    {
        try {
            if ($rating != -1) {
                $query = "UPDATE ratings SET rating = :val WHERE user_id = :user_id AND book_id = :book_id";
            }
            else {
                $query = "UPDATE reviews SET text = :val WHERE user_id = :user_id AND book_id = :book_id";
            }
            $statement = $this->database->prepare($query);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            if ($rating == -1) {
                $statement->bindParam(':val', $review, PDO::PARAM_STR);
            }
            else {
                $statement->bindParam(':val', $rating, PDO::PARAM_INT);
            }
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    private function post(string $action, int $userId, int $bookId, int $rating, string $review): bool
    {   
        try {
            $column = 'rating';
            if ($rating == -1) {
                $column = 'text';
            }
            $query = 'INSERT INTO '.$action.'s (user_id, book_id, '.$column.') VALUES (:user_id, :book_id, :'.$column.')';

            $statement = $this->database->prepare($query);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            if ($rating == -1) {
                $statement->bindParam(':text', $review, PDO::PARAM_STR);
            }
            else {
                $statement->bindParam(':rating', $rating, PDO::PARAM_INT);
            }
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function delete(string $action, int $userId, int $bookId): bool
    {   
        try {
            $query = 'DELETE FROM '.$action.'s WHERE user_id = :user_id AND book_id = :book_id';

            $statement = $this->database->prepare($query);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $statement->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}