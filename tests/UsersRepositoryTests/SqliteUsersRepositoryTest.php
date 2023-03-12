<?php

namespace alxgeras\php2\UnitTests\UsersRepositoryTests;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\UsersRepository\SqliteUsersRepository;
use alxgeras\php2\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
                ':username' => 'username',
                ':firstName' => 'first',
                ':lastName' => 'last'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'),
            new Name('first', 'last'),
            'username'
        );

        $userRepository->save($user);
    }

    /**
     * @throws \alxgeras\php2\Exceptions\InvalidUuidException
     * @throws UserNotFoundException
     */
    public function testItGetUserByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
            'first_name' => 'first',
            'last_name' => 'last',
            'username' => 'username'
        ]);

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        $user = $userRepository->get(new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'));

        $this->assertSame('104e8613-b7b2-4cb9-8296-56a765033ff8', (string)$user->getUuid());
    }

    /**
     * @throws \alxgeras\php2\Exceptions\InvalidUuidException
     */
    public function testItThrowAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('No user: 9dba7ab0-93be-4ff4-9699-165320c97694');

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        $userRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
    }
}