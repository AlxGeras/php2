<?php

namespace alxgeras\php2\Repositories\LikesRepository;

use alxgeras\php2\Blog\Likes\Like;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\LikeAlreadyExists;
use alxgeras\php2\Exceptions\LikeNotFoundException;
use alxgeras\php2\Repositories\Interfaces\CommentsLikesRepositoryInterface;

class SqliteCommentsLikesRepository implements CommentsLikesRepositoryInterface
{
    public function __construct(
        private \PDO $pdo
    )
    {
    }

    public function save(Like $like): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO commentsLikes
                       (uuid, comment_uuid, user_uuid)
                    VALUES 
                       (:uuid, :comment_uuid, :user_uuid)'
        );
        $statement->execute(
            [
                ':uuid' => $like,
                'comment_uuid' => $like->getComment()->getUuid(),
                'user_uuid' => $like->getUser()->getUuid()
            ]
        );
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByCommentUuid(UUID $uuid): array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM commentsLikes
            WHERE comment_uuid = :uuid'
        );
        $statement->execute([':uuid' => $uuid]);

        $result = $statement->fetchAll();

        if (!$result) {
            throw new LikeNotFoundException(
                'No likes to this comment: ' . $uuid
            );
        }

        return $result;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForCommentExists($commentUuid, $userUuid): void
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM commentsLikes
            WHERE 
                comment_uuid = :commentUuid AND user_uuid = :userUuid'
        );

        $likeExists = $statement->execute(
            [
                ':commentUuid' => $commentUuid,
                ':userUuid' => $userUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this comment already exists'
            );
        }
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
                   FROM commentsLikes
                   WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}