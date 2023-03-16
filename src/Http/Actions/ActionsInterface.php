<?php

namespace alxgeras\php2\Http\Actions;

use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;

interface ActionsInterface
{
    public function handle(Request $request): Response;
}