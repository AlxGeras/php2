<?php

namespace alxgeras\php2\Http\Actions\Auth;

use alxgeras\php2\Blog\AuthToken;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\Auth\PasswordAuthenticationInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\AuthTokensRepositoryInterface;

class LogIn implements ActionsInterface
{

    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface   $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            (new \DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokensRepository->save($authToken);

        return new SuccessFulResponse(
            ['token' => (string)$authToken]
        );

    }
}