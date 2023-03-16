<?php

namespace alxgeras\php2\Person;

class Name
{
    public function __construct(
        private string $firstName,
        private string $lastName
    )
    {
    }

    public function __toString(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
}