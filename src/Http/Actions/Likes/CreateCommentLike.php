<?php

namespace alxgeras\php2\Http\Actions\Likes;

use alxgeras\php2\Blog\Likes\CommentLike;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\LikeAlreadyExists;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\CommentsLikesRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\CommentsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;

class CreateCommentLike implements ActionsInterface
{
    public function __construct(
        private CommentsLikesRepositoryInterface $likesRepository,
        private CommentsRepositoryInterface      $commentsRepository,
        private UsersRepositoryInterface      $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->JsonBodyField('comment_uuid');
            $userUuid = $request->JsonBodyField('user_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForCommentExists($commentUuid, $userUuid);
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newLikeUuid = UUID::random();
            $comment = $this->commentsRepository->get(new UUID($commentUuid));
            $user = $this->usersRepository->get(new UUID($userUuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $like = new CommentLike(
            $newLikeUuid,
            $user,
            $comment
        );

        $this->likesRepository->save($like);

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}