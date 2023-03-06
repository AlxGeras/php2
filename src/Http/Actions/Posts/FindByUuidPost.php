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

class FindByUuidPost implements ActionsInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            //$uuid = $request->JsonBodyField('post_uuid');
            $uuid = $request->query('post_uuid');
            $post = $this->postsRepository->get(new UUID($uuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            [
                'title' => $post->getTitle(),
                'text' => $post->getText(),
                'author' => (string)$post->getAutor()
            ]
        );
    }
}