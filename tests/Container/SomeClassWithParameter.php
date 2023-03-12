<?php

namespace alxgeras\php2\UnitTests\Container;

class SomeClassWithParameter
{
    public function __construct(
        private int $value
    )
    {
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}