<?php

namespace alxgeras\php2\Http\Actions\Users;

use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;

class FindByUsername implements ActionsInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
            $user = $this->usersRepository->getByUsername($username);
        } catch (HttpException|UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            [
                'username' => $user->getUsername(),
                'name' => (string)$user->getName()
            ]
        );
    }
}