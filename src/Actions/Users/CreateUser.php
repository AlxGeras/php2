<?php

namespace alxgeras\Php2\Actions\Users;

use alxgeras\Php2\Actions\ActionInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\UsersRepositoryInterface;
use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\HttpException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;
use alxgeras\Php2\http\SuccessfulResponse;
use alxgeras\Php2\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('username'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                )
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid
        ]);
    }
}