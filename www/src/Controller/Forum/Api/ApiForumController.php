<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\Forum\Api;

use Project\Bookworm\Helpers\JsonEncoder;
use Project\Bookworm\Model\Entity\Forum;
use Project\Bookworm\Model\ForumRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ApiForumController
{
    private ForumRepository $forumRepository;
    private JsonEncoder $json;

    public function __construct(ForumRepository $forumRepository, JsonEncoder $json)
    {
        $this->forumRepository = $forumRepository;
        $this->json = $json;
    }

    public function getForums(Request $request, Response $response): Response
    {
        $forums = $this->forumRepository->getAllForums() ?? [];
        return $this->json->encodeToRes($forums, $response);
    }

    public function getForum(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);

        $forum = $this->forumRepository->getForum($id);

        if ($forum == null) {
            $err = ['message' => 'Forum with id ' . $id . ' does not exist'];
            return $this->json->encodeToRes($err, $response)->withStatus(404);
        }

        return $this->json->encodeToRes($forum, $response)->withStatus(200);
    }

    public function deleteForum(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);

        if (!$this->forumRepository->deleteForum($id)) {
            $err = ['message' => 'Forum with id ' . $id . ' does not exist'];
            return $this->json->encodeToRes($err, $response)->withStatus(404);
        }

        return $response->withStatus(200);
    }

    public function createForum(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['title']) || !isset($data['description'])) {
            $err = ['message' => 'A required input was missing.'];
            return $this->json->encodeToRes($err, $response)->withStatus(400);
        }

        $forum = new Forum(null, $data['title'], $data['description']);
        $forum = $this->forumRepository->createForum($forum);

        $response = $this->json->encodeToRes($forum, $response)->withStatus(201);
        return $response->withHeader('Location', '/api/forums/' . $forum->id());
    }
}