<?php

namespace alxgeras\php2\Blog\Likes;

use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;

class PostLike extends Like
{
    private Post $post;

    public function __construct(UUID $uuid, User $user, Post $post)
    {
        parent::__construct($uuid, $user);
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }
}