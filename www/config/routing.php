<?php

declare(strict_types=1);

use Project\Bookworm\Controller\Book\BookCatalogueController;
use Project\Bookworm\Controller\Book\BookDetailsController;
use Project\Bookworm\Controller\Book\RatingController;
use Project\Bookworm\Controller\Book\ReviewController;
use Project\Bookworm\Controller\Forum\Api\ApiForumController;
use Project\Bookworm\Controller\Forum\Api\ApiPostController;
use Project\Bookworm\Controller\Forum\ForumController;
use Project\Bookworm\Controller\Forum\PostController;
use Project\Bookworm\Controller\HomeController;
use Project\Bookworm\Controller\User\LoginController;
use Project\Bookworm\Controller\User\ProfileController;
use Project\Bookworm\Controller\User\RegisterController;
use Project\Bookworm\Middleware\ApiCheckSessionMiddleware;
use Project\Bookworm\Middleware\CheckSessionMiddleware;
use Project\Bookworm\Middleware\SessionMiddleware;

$app->add(SessionMiddleware::class);

$app->get('/', HomeController::class . ':apply')->setName('home');

$app->get('/sign-up', RegisterController::class . ':showForm');
$app->post('/sign-up', RegisterController::class . ':handleFormSubmission')->setName('handle-register');

$app->get('/sign-in', LoginController::class . ':showForm');
$app->post('/sign-in', LoginController::class . ':handleFormSubmission')->setName('handle-login');

$app->get('/profile', ProfileController::class . ':showForm')->add(CheckSessionMiddleware::class);
$app->post('/profile', ProfileController::class . ':handleFormSubmission')->setName('handle-profile')->add(CheckSessionMiddleware::class);

$app->get('/catalogue', BookCatalogueController::class . ':showCatalogue')->setName('get-book-catalogue')->add(CheckSessionMiddleware::class);
$app->post('/catalogue', BookCatalogueController::class . ':handleFormSubmission')->setName('post-book-catalogue')->add(CheckSessionMiddleware::class);

$app->get('/catalogue/{id}', BookDetailsController::class . ':displayBook')->setName('book-details')->add(CheckSessionMiddleware::class);

$app->put('/catalogue/{id}/rate', RatingController::class . ':putRating')->add(CheckSessionMiddleware::class);
$app->delete('/catalogue/{id}/rate', RatingController::class . ':deleteRating')->add(CheckSessionMiddleware::class);

$app->put('/catalogue/{id}/review', ReviewController::class . ':putReview')->add(CheckSessionMiddleware::class);
$app->delete('/catalogue/{id}/review', ReviewController::class . ':deleteReview')->add(CheckSessionMiddleware::class);

$app->get('/forums', ForumController::class . ':showForums')->setName('show-forums')->add(CheckSessionMiddleware::class);

$app->get('/api/forums', ApiForumController::class . ':getForums')->setName('get-forums')->add(ApiCheckSessionMiddleware::class);
$app->post('/api/forums', ApiForumController::class . ':createForum')->setName('create-forum')->add(ApiCheckSessionMiddleware::class);

$app->get('/api/forums/{id}', ApiForumController::class . ':getForum')->setName('get-forum')->add(ApiCheckSessionMiddleware::class);
$app->delete('/api/forums/{id}', ApiForumController::class . ':deleteForum')->setName('delete-forum')->add(ApiCheckSessionMiddleware::class);

$app->get('/forums/{id}/posts', PostController::class . ':showPosts')->setName('show-posts')->add(CheckSessionMiddleware::class);

$app->get('/api/forums/{id}/posts', ApiPostController::class . ':getPosts')->setName('get-posts')->add(ApiCheckSessionMiddleware::class);
$app->post('/api/forums/{id}/posts', ApiPostController::class . ':createPost')->setName('create-post')->add(ApiCheckSessionMiddleware::class);