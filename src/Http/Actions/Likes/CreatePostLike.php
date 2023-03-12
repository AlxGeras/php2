<?php

namespace alxgeras\php2\Http\Actions\Likes;

use alxgeras\php2\Blog\Likes\PostLike;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\LikeAlreadyExists;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\Auth\IdentificationInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\PostsLikesRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;

class CreatePostLike implements ActionsInterface
{

    public function __construct(
        private PostsLikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface      $postsRepository,
        private IdentificationInterface $identification
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->identification->user($request);
            $postUuid = $request->JsonBodyField('post_uuid');
        } catch (HttpException|AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists($postUuid, $user->getUuid());
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newLikeUuid = UUID::random();
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $like = new PostLike(
            $newLikeUuid,
            $user,
            $post
        );

        $this->likesRepository->save($like);

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}