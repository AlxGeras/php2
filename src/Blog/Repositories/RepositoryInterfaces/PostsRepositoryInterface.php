<?php

namespace alxgeras\Php2\Blog\Repositories\RepositoryInterfaces;

use alxgeras\Php2\Blog\Post;
use alxgeras\Php2\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function getByTitle(string $title): Post;
}