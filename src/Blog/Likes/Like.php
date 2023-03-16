<?php

namespace alxgeras\php2\Blog\Likes;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;

class Like
{
    public function __construct(
        protected UUID $uuid,
        protected User $user
    )
    {
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function __toString(): string
    {
        return $this->getUuid();
    }
}