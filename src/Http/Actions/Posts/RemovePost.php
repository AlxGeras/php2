<?php

namespace alxgeras\php2\Http\Actions\Posts;

use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;

class RemovePost implements ActionsInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->postsRepository->remove($uuid);
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            ['action' => 'post ' . $uuid . ' deleted successfully']
        );
    }
}