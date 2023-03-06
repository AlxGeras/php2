<?php

namespace alxgeras\Php2\UnitTests\Blog;

use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUuidNotCorrect():void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Malformed UUID: 1234");
        new UUID('1234');
    }
}