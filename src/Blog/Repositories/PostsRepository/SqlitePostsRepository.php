<?php

namespace alxgeras\Php2\Blog\Repositories\PostsRepository;

use alxgeras\Php2\Blog\Post;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\PostsRepositoryInterface;
use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use alxgeras\Php2\Exceptions\PostNotFoundException;
use alxgeras\Php2\Person\Name;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private \PDO $connection,
    )
    {
    }

    /**
     * @throws InvalidArgumentException|PostNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $postInfo): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot get post: $postInfo"
            );
        }

        $user = new User(
            new UUID($result['author_uuid']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':author_uuid' => (string)$post->getUser()->getUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT posts.*, users.username, users.first_name, users.last_name 
                    FROM posts LEFT JOIN users
                    ON posts.author_uuid = users.uuid
                    WHERE posts.uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    public function getByTitle(string $title): Post
    {
        $statement = $this->connection->prepare(
            'SELECT posts.*, users.username, users.first_name, users.last_name 
                    FROM posts LEFT JOIN users
                    ON posts.author_uuid = users.uuid
                    WHERE posts.title = :title'
        );

        $statement->execute([
            ':title' => $title,
        ]);
        return $this->getPost($statement, $title);
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE posts.uuid=:uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }
}