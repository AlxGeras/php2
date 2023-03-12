<?php

namespace alxgeras\php2\Blog\Commands;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\ArgumentsException;
use alxgeras\php2\Exceptions\CommandException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Auth\IdentificationInterface;
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
            throw new CommandException("User already exists: $username");
        }

        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            $uuid,
            new Name($arguments->get('first_name'), $arguments->get('last_name')),
            $username,
        ));

        $this->logger->info('User created: ' . $uuid);
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