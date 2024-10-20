<?php

declare(strict_types=1);

use DI\Container;
use Project\Bookworm\Helpers\JsonEncoder;
use Project\Bookworm\Model\ForumRepository;
use Project\Bookworm\Model\Repository\MysqlForumRepository;
use Psr\Container\ContainerInterface;
use Project\Bookworm\Model\Repository\MysqlUserRepository;
use Project\Bookworm\Model\Repository\MysqlBookRepository;
use Project\Bookworm\Model\Repository\ApiBookISBNRepository;
use Project\Bookworm\Model\Repository\PDOSingleton;
use Project\Bookworm\Model\UserRepository;
use Project\Bookworm\Model\BookRepository;
use Project\Bookworm\Model\BookISBNRepository;
use Slim\Views\Twig;
use Slim\Flash\Messages;

$container = new Container();

$container->set('view', function () {
    return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
});

$container->set(Twig::class, function (ContainerInterface $c) {
    return $c->get('view');
});

$container->set(Messages::class,  function () {
    return new Messages();
});

$container->set(PDO::class, function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    )->connection();
});

$container->set(UserRepository::class, function (ContainerInterface $c) {
    return $c->get(MysqlUserRepository::class);
});

$container->set(BookRepository::class, function (ContainerInterface $c) {
    return $c->get(MysqlBookRepository::class);
});

$container->set(BookISBNRepository::class, function (ContainerInterface $c) {
    return $c->get(ApiBookISBNRepository::class);
});

$container->set(ForumRepository::class, function (ContainerInterface $c) {
    return $c->get(MysqlForumRepository::class);
});

$container->set(JsonEncoder::class, function (ContainerInterface $c) {
    return new JsonEncoder();
});