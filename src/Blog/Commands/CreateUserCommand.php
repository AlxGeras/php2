<?php

namespace alxgeras\php2\Blog\Commands;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Exceptions\ArgumentsException;
use alxgeras\php2\Exceptions\CommandException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface          $logger
    )
    {
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     * @throws InvalidUuidException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            return;
        }

        $password = $arguments->get('password');

        $user = User::createFrom(
            $username,
            $password,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            ),
        );

        $this->usersRepository->save($user);
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}