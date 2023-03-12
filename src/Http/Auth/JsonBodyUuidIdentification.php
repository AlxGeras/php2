<?php

namespace alxgeras\php2\Http\Auth;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $uuid = new UUID($request->JsonBodyField('user_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->repository->get($uuid);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}