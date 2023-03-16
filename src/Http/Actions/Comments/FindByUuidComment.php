<?php

namespace alxgeras\php2\Http\Actions\Comments;

use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\CommentsRepositoryInterface;

class FindByUuidComment implements ActionsInterface
{

    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->JsonBodyField('comment_uuid');
            $comment = $this->commentsRepository->get(new UUID($uuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            [
                'post_uuid' => (string)($comment->getPost())->getUuid(),
                'txt' => $comment->getText(),
                'author' => (string)$comment->getUser()
            ]
        );
    }
}