<?php

namespace alxgeras\Php2\Actions;

use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;


}