<?php

namespace alxgeras\php2\Repositories\Interfaces;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;

interface UsersRepositoryInterface
{
    public function get(UUID $uuid): User;
    public function save(User $user): void;
    public function getByUsername(string $username): User;
    public function remove(UUID $uuid): void;
}