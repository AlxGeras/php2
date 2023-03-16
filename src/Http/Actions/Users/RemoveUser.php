<?php

namespace alxgeras\php2\Http\Actions\Users;

use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Http\Actions\ActionsInterface;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\Response;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class RemoveUser implements ActionsInterface
{

    public function __construct(
        private UsersRepositoryInterface $repository,
        private LoggerInterface          $logger
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('user_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $this->repository->remove($uuid);

        return new SuccessFulResponse(
            ['action' => 'user ' . $uuid . ' deleted successfully']
        );
    }
}