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

class RemoveComment implements ActionsInterface
{

    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->commentsRepository->remove($uuid);
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            ['action' => 'comment ' . $uuid . ' deleted successfully']
        );
    }
}