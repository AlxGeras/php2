<?php

namespace alxgeras\php2\Blog\Likes;

use alxgeras\php2\Blog\Comment;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;

class CommentLike extends Like
{
    private Comment $comment;

    public function __construct(UUID $uuid, User $user, Comment $comment)
    {
        parent::__construct($uuid, $user);
        $this->comment = $comment;
    }

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }
}