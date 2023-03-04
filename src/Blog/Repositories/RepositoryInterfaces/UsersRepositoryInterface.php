<?php

namespace alxgeras\Php2\Blog\Repositories\RepositoryInterfaces;

use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}