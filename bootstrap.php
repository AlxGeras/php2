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
use alxgeras\php2\Http\Auth\AuthenticationInterface;
use alxgeras\php2\Http\Auth\JsonBodyUsernameAuthentication;
use alxgeras\php2\Http\Auth\PasswordAuthenticationInterface;
use alxgeras\php2\Http\Auth\PasswordAuthentication;
use alxgeras\php2\Repositories\Interfaces\AuthTokensRepositoryInterface;
use alxgeras\php2\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use alxgeras\php2\Http\Auth\TokenAuthenticationInterface;
use alxgeras\php2\Http\Auth\BearerTokenAuthentication;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Text;
use Faker\Provider\Lorem;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$faker = new \Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Lorem($faker));

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

$container->bind(
    \Faker\Generator::class,
    $faker
);

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
    AuthenticationInterface::class,
    JsonBodyUsernameAuthentication::class
);

$container->bind(
    \PDO::class,
    new \PDO(
        $_SERVER['SQLITE_DB_PATH'],
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

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

return $container;