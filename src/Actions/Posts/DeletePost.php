<?php

namespace alxgeras\Php2\Actions\Posts;

use alxgeras\Php2\Actions\ActionInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\PostsRepositoryInterface;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\HttpException;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use alxgeras\Php2\Exceptions\PostNotFoundException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\SuccessfulResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->jsonBodyField('uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->postsRepository->get($uuid);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->delete($uuid);

        return new SuccessfulResponse([
            'uuid' => (string)$uuid,
        ]);
    }
}