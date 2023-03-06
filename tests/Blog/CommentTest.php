<?php

namespace alxgeras\php2\UnitTests\Blog;

use alxgeras\php2\Blog\Comment;
use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Person\Name;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    /**
     * @throws InvalidUuidException
     */
    public function testItReturnsCommentAsString(): void
    {
        $user = new User(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            new Name('first', 'last'),
            'username'
        );

        $post = new Post(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            $user,
            'title',
            'text'
        );

        $comment = new Comment(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            $user,
            $post,
            'txt'
        );

        $value = (string)$comment;
        $this->assertIsString($value);
    }
}