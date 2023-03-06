<?php

namespace alxgeras\php2\Http\Actions\Likes;

use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\CommentsLikesRepositoryInterface;

class RemoveCommentLike implements ActionsInterface
{
    public function __construct(
        private CommentsLikesRepositoryInterface $likesRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->likesRepository->remove($uuid);
        }catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            ['data' => 'likes ' . $uuid . ' deleted successfully']
        );
    }
}