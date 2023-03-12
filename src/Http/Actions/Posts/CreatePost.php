<?php

namespace alxgeras\php2\Http\Actions\Posts;

use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\Auth\IdentificationInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionsInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private IdentificationInterface  $identification,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->identification->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->JsonBodyField('title'),
                $request->JsonBodyField('text')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        return new SuccessFulResponse(
            ['uuid' => (string)$newPostUuid]
        );
    }
}