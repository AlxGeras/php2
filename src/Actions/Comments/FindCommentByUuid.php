<?php

namespace alxgeras\Php2\Actions\Comments;

use alxgeras\Php2\Actions\ActionInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\CommentsRepositoryInterface;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\CommentNotFoundException;
use alxgeras\Php2\Exceptions\HttpException;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;
use alxgeras\Php2\http\SuccessfulResponse;

class FindCommentByUuid implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $commentUuid = new UUID($uuid);
        } catch (InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $comment = $this->commentsRepository->get($commentUuid);
        } catch (CommentNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $comment->getUuid(),
            'author_uuid' => $comment->getUser()->getUuid(),
            'post_uuid' => $comment->getPost()->getUuid(),
            'text' => $comment->getText()
        ]);
    }
}