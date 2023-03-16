<?php

namespace alxgeras\php2\Http\Actions\Auth;

use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\AuthTokenNotFoundException;
use alxgeras\php2\Exceptions\AuthTokenRepositoryException;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\AuthTokensRepositoryInterface;

class LogOut implements ActionsInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    /**
     * @throws HttpException
     * @throws AuthException
     * @throws AuthTokenRepositoryException
     */
    public function handle(Request $request): Response
    {
        $header = $request->header('Authorization');

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException(
                "Malformed token: [$header]"
            );
        }

        $token = mb_substr(
            $header,
            strlen(self::HEADER_PREFIX)
        );

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $expired = new \DateTimeImmutable();

        $authToken->setExpiresOn($expired);

        $this->authTokensRepository->save($authToken);

        return new SuccessFulResponse(
            ['Token expired' => "[$token]"]
        );
    }
}