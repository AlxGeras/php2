<?php

namespace alxgeras\Php2\Blog\Repositories\RepositoryInterfaces;

use alxgeras\Php2\Blog\Comment;
use alxgeras\Php2\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}