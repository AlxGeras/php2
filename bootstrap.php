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
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use alxgeras\php2\Http\Auth\IdentificationInterface;
use alxgeras\php2\Http\Auth\JsonBodyUsernameIdentification;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/logs/blog.log'
            )
        )
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/logs/blog.error.log',
                level: Logger::ERROR,
                bubble: false
            )
        );
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler('php://stdout')
        );
}

$container = new DIContainer();
/*
$container->bind(
    LoggerInterface::class,
    (new Logger('blog'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
// Добавили новый обработчик:
        ->pushHandler(new StreamHandler(
// записывать в файл "blog.error.log"
            __DIR__ . '/logs/blog.error.log',
// события с уровнем ERROR и выше,
            level: Logger::ERROR,
// при этом событие не должно "всплывать"
            bubble: false,
        ))->pushHandler(
            new StreamHandler("php://stdout")
)

);
*/

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
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);


$container->bind(
    \PDO::class,
    new \PDO(
        'sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'],
        null,
        null,
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    )
);

$container->bind(
    LoggerInterface::class,
    $logger
);

return $container;