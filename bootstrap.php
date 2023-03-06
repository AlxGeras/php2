<?php

use alxgeras\php2\Container\DIContainer;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use alxgeras\php2\Repositories\UsersRepository\SqliteUsersRepository;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;
use alxgeras\php2\Repositories\PostsRepository\SqlitePostsRepository;
use alxgeras\php2\Repositories\Interfaces\CommentsRepositoryInterface;
use alxgeras\php2\Repositories\CommentsRepository\SqliteCommentsRepository;
use alxgeras\php2\Repositories\Interfaces\PostsLikesRepositoryInterface;
use alxgeras\php2\Repositories\LikesRepository\SqlitePostsLikesRepository;
use alxgeras\php2\Repositories\Interfaces\CommentsLikesRepositoryInterface;
use alxgeras\php2\Repositories\LikesRepository\SqliteCommentsLikesRepository;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    PostsLikesRepositoryInterface::class,
    SqlitePostsLikesRepository::class
);

$container->bind(
    CommentsLikesRepositoryInterface::class,
    SqliteCommentsLikesRepository::class
);

$container->bind(
    \PDO::class,
    new \PDO(
        'sqlite:blog.sqlite',
        null,
        null,
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    )
);

return $container;