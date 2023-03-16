<?php

namespace alxgeras\php2\Repositories\Interfaces;

use alxgeras\php2\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;

    public function get(string $token): AuthToken;
}