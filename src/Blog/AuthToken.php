<?php

namespace alxgeras\php2\Blog;

class AuthToken
{

    public function __construct(
        private string             $token,
        private UUID               $userUuid,
        private \DateTimeImmutable $expiresOn
    )
    {
    }


    public function __toString(): string
    {
        return (string)$this->token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpiresOn(): \DateTimeImmutable
    {
        return $this->expiresOn;
    }

    /**
     * @param \DateTimeImmutable $expiresOn
     */
    public function setExpiresOn(\DateTimeImmutable $expiresOn): void
    {
        $this->expiresOn = $expiresOn;
    }
}