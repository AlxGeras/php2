<?php

namespace alxgeras\Php2\Actions\Posts;

use alxgeras\Php2\Actions\ActionInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\PostsRepositoryInterface;
use alxgeras\Php2\Exceptions\HttpException;
use alxgeras\Php2\Exceptions\PostNotFoundException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;
use alxgeras\Php2\http\SuccessfulResponse;

class FindPostByTitle implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $title = $request->query('title');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $post = $this->postsRepository->getByTitle($title);
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $post->getUuid(),
            'author_uuid' => $post->getUser()->getUuid(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
            'username' => $post->getUser()->getUsername(),
            'first_name' => $post->getUser()->getName()->getFirstName(),
            'last_name' => $post->getUser()->getName()->getLastName(),
        ]);
    }
}