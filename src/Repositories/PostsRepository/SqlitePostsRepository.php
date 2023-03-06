<?php

namespace alxgeras\php2\Repositories\PostsRepository;

use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Exceptions\PostNotFoundException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private \PDO $pdo
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
    }

    /**
     * @throws AppException
     */
    public function get(UUID $uuid): Post
    {
        $postResult = $this->query(
            'posts',
            $uuid,
            new PostNotFoundException("Поста с uuid: $uuid нет")
        );

        $userUuid = new UUID($postResult['user_uuid']);

        $userResult = $this->query(
            'users',
            $userUuid,
            new UserNotFoundException('Пользователя с uuid: ' . $postResult['user_uuid'] . ' нет')
        );

        return new Post(
            $uuid,
            new User(
                $userUuid,
                new Name($userResult['first_name'], $userResult['last_name']),
                $userResult['username']
            ),
            $postResult['title'],
            $postResult['text']);
    }

    /**
     * @throws AppException
     */
    private function query(
        string       $table,
        UUID         $uuid,
        AppException $exceptionName
    ): array
    {
        $statement = $this->pdo->prepare(
            "SELECT *
                   FROM $table
                   WHERE $table.uuid = :uuid"
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch();

        if (!$result) {
            throw $exceptionName;
        }

        return $result;
    }

    /**
     * @throws AppException
     */
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