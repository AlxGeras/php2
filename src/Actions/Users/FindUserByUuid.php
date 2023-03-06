<?php

namespace alxgeras\Php2\Actions\Users;

use alxgeras\Php2\Actions\ActionInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\UsersRepositoryInterface;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\HttpException;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use alxgeras\Php2\Exceptions\UserNotFoundException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;
use alxgeras\Php2\http\SuccessfulResponse;

class FindUserByUuid implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $uuidClass = new UUID($uuid);
        } catch (InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $user = $this->usersRepository->get($uuidClass);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $user->getUuid(),
            'username' => $user->getUsername(),
            'first_name' => $user->getName()->getFirstName(),
            'last_name' => $user->getName()->getLastName()
        ]);
    }
}