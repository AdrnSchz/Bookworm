<?php

namespace Project\Bookworm\Helpers;

use Slim\Psr7\Response;

final class JsonEncoder
{
    public function encode($data): Response {
        $response = new Response();
        return $this->encodeToRes($data, $response);
    }

    public function encodeToRes($data, $response): Response {

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write((string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR)
        );

        return $response;
    }
}