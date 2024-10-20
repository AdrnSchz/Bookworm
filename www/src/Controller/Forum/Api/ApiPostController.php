<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller\Forum\Api;

use Project\Bookworm\Helpers\JsonEncoder;
use Project\Bookworm\Model\Entity\Post;
use Project\Bookworm\Model\ForumRepository;
use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ApiPostController
{
    private ForumRepository $forumRepository;
    private UserRepository $userRepository;
    private JsonEncoder $json;

    public function __construct(ForumRepository $forumRepository, UserRepository $userRepository, JsonEncoder $json)
    {
        $this->forumRepository = $forumRepository;
        $this->userRepository = $userRepository;
        $this->json = $json;
    }

    public function getPosts(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);

        if ($this->forumRepository->getForum($id) == null) {
            $err = ['message' => 'Forum with id ' . $id . ' does not exist'];
            return $this->json->encodeToRes($err, $response)->withStatus(404);
        }

        $posts = $this->forumRepository->getAllPosts($id) ?? [];

        foreach ($posts as $post) {
            $user = $this->userRepository->getUserById($post->userId());
            $post->setUser($user);
        }

        return $this->json->encodeToRes($posts, $response);
    }

    public function createPost(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['forumId']) || !isset($data['userId']) || !isset($data['title']) || !isset($data['contents'])) {
            $err = ['message' => 'A required input was missing.'];
            return $this->json->encodeToRes($err, $response)->withStatus(400);
        }

        $post = new Post(null, $data['forumId'], $data['userId'], $data['title'], $data['contents'], null, null);
        $post = $this->forumRepository->createPost($post);

        $user = $this->userRepository->getUserById($post->userId());
        $post->setUser($user);

        $response = $this->json->encodeToRes($post, $response)->withStatus(201);
        return $response->withHeader('Location', '/api/forums/' . $data['forumId'] . '/posts/' . $post->id());
    }
}