<?php

namespace alxgeras\php2\Http;

class SuccessFulResponse extends Response
{
    protected const SUCCESS = true;

    public function __construct(
        private array $data = [],
    )
    {
    }

    public function payload(): array
    {
        return ['data' => $this->data];
    }
}