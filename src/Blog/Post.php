<?php

namespace alxgeras\Php2\Blog;

use alxgeras\Php2\Blog\User;

class Post
{
    public function __construct(
        private UUID $uuid,
        private User $user,
        private string $title,
        private string $text
    )
    {

    }

    public function __toString(): string
    {
        return $this->user->getUsername() . ' пишет: ' . $this->title . PHP_EOL . $this->text;
    }

    /**
     * @return UUID
     */
    public function getUuid(): string
    {
        return (string)$this->uuid;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
