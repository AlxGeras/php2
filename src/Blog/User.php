<?php

namespace alxgeras\php2\Blog;

use alxgeras\php2\Person\Name;

class User
{
    public function __construct(
        private UUID   $uuid,
        private string $username,
        private string $hashedPassword,
        private Name   $name,
    )
    {
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }

    public static function createFrom(
        string $username,
        string $password,
        Name   $name
    ): self
    {
        $uuid = UUID::random();

        return new self(
            $uuid,
            $username,
            self::hash(
                $password,
                $uuid),
            $name
        );
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}