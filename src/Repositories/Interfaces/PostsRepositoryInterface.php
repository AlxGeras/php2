<?php

namespace alxgeras\php2\Repositories\Interfaces;

use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;

    public function get(UUID $uuid): Post;

    public function remove(UUID $uuid): void;
}