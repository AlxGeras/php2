<?php

namespace alxgeras\php2\Http\Actions\Comments;

use alxgeras\php2\Blog\Comment;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\Auth\IdentificationInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\CommentsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;

class CreateComment implements ActionsInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface    $postsRepository,
        private IdentificationInterface $identification
    )
    {
    }

    /**
     * @throws InvalidUuidException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->identification->user($request);
            $postUuid = $request->JsonBodyField('post_uuid');
            $txt = $request->JsonBodyField('text');
        } catch (HttpException|AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newUuidComment = UUID::random();

        $comment = new Comment(
            $newUuidComment,
            $user,
            $post,
            $txt
        );

        $this->commentsRepository->save($comment);

        return new SuccessFulResponse(
            ['data' => (string)$newUuidComment]
        );
    }
}