<?php

namespace alxgeras\php2\UnitTests\Blog;

use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\InvalidUuidException;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    public function testItThrowAnExceptionWhenBagFormatUuid(): void
    {
        $value = 'a3a17b83c9164932b1c2';

        $this->expectException(InvalidUuidException::class);
        $this->expectExceptionMessage('Incorrect format UUID: a3a17b83c9164932b1c2');

        new UUID($value);
    }

    /**
     * @throws InvalidUuidException
     */
    public function testItReturnsRandomUuidRequiredFormat(): void
    {
        $uuid = UUID::random();

        $this->assertStringMatchesFormat('%x-%x-%x-%x-%x', $uuid);
    }
}