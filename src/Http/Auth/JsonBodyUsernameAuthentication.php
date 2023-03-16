<?php

namespace alxgeras\php2\Http\Auth;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;

class JsonBodyUsernameAuthentication implements AuthenticationInterface
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
            $username = $request->JsonBodyField('username');
        }catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->repository->getByUsername($username);
        }catch (UserNotFoundException|InvalidUuidException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}