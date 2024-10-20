<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use Exception;
use Project\Bookworm\Model\BookISBNRepository;
use Project\Bookworm\Model\Entity\Book;

final class ApiBookISBNRepository implements BookISBNRepository
{
    /**
     * @throws Exception
     */
    private function getJSONFromUrl(string $urlAPI) {

        $file = @file_get_contents($urlAPI);
        if ($file === false) {
            throw new Exception();
        }

        $response = json_decode($file, TRUE);
        if ($response === null) {
            throw new Exception();
        }
        return $response;
    }

    public function getBookByIsbn(string $isbn): Book
    {   
        try {
            $response = $this->getJSONFromUrl('https://openlibrary.org/isbn/'.$isbn.'.json');
            
            $num_pages = 0;
            if (isset($response['number_of_pages'])) {
                $num_pages = $response['number_of_pages'];
            }
            $worksEndPoint = $response['works'][0]['key'];
            $coverURL = 'https://covers.openlibrary.org/b/isbn/'.$isbn.'-L.jpg';
            
            $response = $this->getJSONFromUrl('https://openlibrary.org'.$worksEndPoint.'.json');

            $title = $response['title'];
            $description = '';
            if (isset($response['description']['type'])) {
                $description = $response['description']['type'];
            }
            else if (isset($response['description'])) {
                $description = $response['description'];
            }
            $authorEndpoint = $response['authors'][0]['author']['key'];
            
            $response = $this->getJSONFromUrl('https://openlibrary.org'.$authorEndpoint.'.json');

            $author = $response['name'];

            return new Book(0, $title, $author, $description, $num_pages, $coverURL);
        }
        catch (Exception $e) {
            return new Book(-1, '', '', '', -1, '');
        }
    }
}