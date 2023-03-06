<?php

namespace alxgeras\Php2\Blog\Commands;

use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\UsersRepositoryInterface;
use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\ArgumentsException;
use alxgeras\Php2\Exceptions\CommandException;
use alxgeras\Php2\Exceptions\UserNotFoundException;
use alxgeras\Php2\Person\Name;

class CreateUserCommand
{
    // Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {

    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            $username,
            new Name($arguments->get('first_name'), $arguments->get('last_name'))
        ));
    }

    private function userExists(string $username): bool
    {
        try {
        // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }

}