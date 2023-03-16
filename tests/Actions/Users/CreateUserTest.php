<?php

namespace alxgeras\php2\UnitTests\Actions\Users;

use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Actions\Users\CreateUser;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use alxgeras\php2\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class CreateUserTest extends TestCase
{
    public function testItRequiresPassword(): void
    {
        $usersRepositoryStub = $this->createStub(UsersRepositoryInterface::class);

        $usersRepositoryStub
            ->method('getByUsername')
            ->willThrowException(
                new UserNotFoundException('No user')
            );

        $action = new CreateUser(
            $usersRepositoryStub,
            new DummyLogger()
        );

        $request = new Request(
            [],
            [],
            '{
                "username": "username",
                "first_name": "first",
                "last_name": "last"
                }'
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('No such field: password');

        $action->handle($request);
    }
}