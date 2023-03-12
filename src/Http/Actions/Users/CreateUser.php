<?php

namespace alxgeras\php2\Http\Actions\Users;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUser implements ActionsInterface
{

    public function __construct(
        private UsersRepositoryInterface $repository,
        private LoggerInterface          $logger
    )
    {
    }

    /**
     * @throws InvalidUuidException
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {
        $username = $request->JsonBodyField('username');

        if ($this->userExists($username)) {
            $message = "This $username already exists";

            $this->logger->warning($message);
            return new ErrorResponse($message);
        }

        $first = $request->JsonBodyField('first_name');
        $last = $request->JsonBodyField('last_name');

        $uuid = UUID::random();

        $user = new User(
            $uuid,
            new Name($first, $last),
            $username
        );

        $this->repository->save($user);

        return new SuccessFulResponse(
            ['username' => $username]
        );

    }

    private function userExists(string $username): bool
    {
        try {
            $this->repository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}