<?php

namespace alxgeras\php2\Repositories\PostsRepository;

use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\PostNotFoundException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private \PDO            $pdo,
        private LoggerInterface $logger
    )
    {

    }

    public function save(Post $post): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO posts
                (uuid, title, text, user_uuid)
                VALUES 
                    (:uuid, :title, :text, :user_uuid)'
        );
        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
            ':user_uuid' => (string)$post->getAutor()->getUuid()
        ]);

        $this->logger->info("Post created: {$post->getUuid()}");
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     */
    public function get(UUID $uuid): Post
    {
        $postResult = $this->query(
            'posts',
            $uuid,
        );

        if (!$postResult) {
            $message = "No post: $uuid";

            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        $userUuid = new UUID($postResult['user_uuid']);

        $userResult = $this->query(
            'users',
            $userUuid,
        );

        return new Post(
            $uuid,
            new User(
                $userUuid,
                $userResult['username'],
                $userResult['password'],
                new Name(
                    $userResult['first_name'],
                    $userResult['last_name']
                ),
            ),
            $postResult['title'],
            $postResult['text']);
    }

    private function query(string $table, UUID $uuid,): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT *
                   FROM $table
                   WHERE $table.uuid = :uuid"
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        return $statement->fetch() ?: null;
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
                   FROM posts
                   WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}