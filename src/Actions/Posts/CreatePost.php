<?php

namespace alxgeras\Php2\Actions\Posts;

use alxgeras\Php2\Actions\ActionInterface;
use alxgeras\Php2\Blog\Post;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\PostsRepositoryInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\UsersRepositoryInterface;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\HttpException;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use alxgeras\Php2\Exceptions\UserNotFoundException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;
use alxgeras\Php2\http\SuccessfulResponse;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}