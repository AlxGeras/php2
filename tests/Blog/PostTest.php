<?php

namespace alxgeras\php2\UnitTests\Blog;

use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Person\Name;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /**
     * @throws InvalidUuidException
     */
    public function testItReturnsPostAsString(): void
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

        $value = (string)$post;
        $this->assertIsString($value);
    }
}