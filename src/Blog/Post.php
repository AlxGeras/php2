<?php

namespace alxgeras\php2\Blog;

class Post
{
    public function __construct(
        private UUID   $uuid,
        private User   $autor,
        private string $title,
        private string $text
    )
    {
    }

    public function __toString(): string
    {
        return $this->getTitle() . PHP_EOL . $this->getText();
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return \alxgeras\php2\Blog\User
     */
    public function getAutor(): User
    {
        return $this->autor;
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