<?php

namespace alxgeras\php2\Http\Actions\Likes;

use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\LikeNotFoundException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\CommentsLikesRepositoryInterface;

class FindByUuidCommentLikes implements ActionsInterface
{

    public function __construct(
        private CommentsLikesRepositoryInterface $repository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->jsonBodyField('comment_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $likes = $this->repository->getByCommentUuid($uuid);
        } catch (LikeNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $outputMas = [];

        foreach ($likes as $like) {
            $outputMas[] = [
                'uuid' => $like['uuid'],
                'user_uuid' => $like['user_uuid']
            ];
        }

        return new SuccessFulResponse(
            ['comment_likes' => $outputMas]
        );
    }
}