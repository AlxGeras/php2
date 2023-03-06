<?php

namespace alxgeras\php2\Repositories\Interfaces;

use alxgeras\php2\Blog\Comment;
use alxgeras\php2\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;
}