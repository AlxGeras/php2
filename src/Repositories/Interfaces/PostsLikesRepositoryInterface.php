<?php

namespace alxgeras\php2\Repositories\Interfaces;

use alxgeras\php2\Blog\Likes\Like;
use alxgeras\php2\Blog\UUID;

interface PostsLikesRepositoryInterface
{
    public function save(Like $like): void;

    public function getByPostUuid(UUID $uuid): array;
}