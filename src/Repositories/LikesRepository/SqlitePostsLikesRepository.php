<?php

namespace alxgeras\php2\Repositories\LikesRepository;

use alxgeras\php2\Blog\Likes\Like;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\LikeAlreadyExists;
use alxgeras\php2\Exceptions\LikeNotFoundException;
use alxgeras\php2\Repositories\Interfaces\PostsLikesRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqlitePostsLikesRepository implements PostsLikesRepositoryInterface
{

    public function __construct(
        private \PDO            $pdo,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Like $like): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO postsLikes
                       (uuid, post_uuid, user_uuid)
                    VALUES 
                       (:uuid, :post_uuid, :user_uuid)'
        );
        $statement->execute(
            [
                ':uuid' => $like,
                'post_uuid' => $like->getPost()->getUuid(),
                'user_uuid' => $like->getUser()->getUuid()
            ]
        );

        $this->logger->info("Like created: $like");
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(UUID $uuid): array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM postsLikes
            WHERE post_uuid = :uuid'
        );
        $statement->execute([':uuid' => $uuid]);

        $result = $statement->fetchAll();

        if (!$result) {
            $message = 'No likes to this post: ' . $uuid;

            $this->logger->warning($message);
            throw new LikeNotFoundException($message);
        }

        return $result;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForPostExists(string $postUuid, string $userUuid): void
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM postsLikes
            WHERE 
                post_uuid = :postUuid AND user_uuid = :userUuid'
        );

        $statement->execute(
            [
                ':postUuid' => $postUuid,
                ':userUuid' => $userUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this post already exists'
            );
        }
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
                   FROM postsLikes
                   WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}