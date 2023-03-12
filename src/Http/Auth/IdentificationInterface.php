<?php

namespace alxgeras\php2\Http\Auth;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}