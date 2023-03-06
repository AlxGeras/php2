<?php

namespace alxgeras\php2\Repositories\UsersRepository;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private array $users = []
    )
    {

    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$uuid === (string)$user->getUuid()) {
                return $user;
            }
        }
        throw new UserNotFoundException(
            'Пользователя с данным uuid: ' . $uuid . ' нет'
        );
    }

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($username === $user->getUsername) {
                return $user;
            }
        }
        throw new UserNotFoundException(
            'Пользователя с таким логином: ' . $username . ' нет'
        );
    }
}