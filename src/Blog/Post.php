<?php

namespace alxgeras\Php2\Blog;

use alxgeras\Php2\Person\Person;

class Post
{
    public function __construct(
        private UUID $uuid,
        private UUID $authorUuid,
        private string $title,
        private string $text
    )
    {

    }

    public function __toString(): string
    {
        return $this->uuid . ' пишет: ' . $this->title . PHP_EOL . $this->text;
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return UUID
     */
    public function getAuthorUuid(): UUID
    {
        return $this->authorUuid;
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
