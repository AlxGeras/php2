<?php

namespace alxgeras\php2\UnitTests\Blog;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Person\Name;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @throws InvalidUuidException
     */
    public function testItReturnsUserAsString(): void
    {
        $user = new User(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            'username',
            bin2hex(random_bytes(40)),
            new Name('first', 'last'),
        );

        $value = (string)$user;
        $this->assertIsString($value);
    }
}