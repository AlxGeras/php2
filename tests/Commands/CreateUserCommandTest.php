<?php

namespace alxgeras\php2\UnitTests\Commands;

use alxgeras\php2\Blog\Commands\Users\CreateUser;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use alxgeras\php2\Blog\User;

class CreateUserCommandTest extends TestCase
{
    public function testItRequiresLastName(): void
    {

        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "password, first_name, last_name"'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface {
            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }

            public function remove(UUID $uuid): void
            {
            }
        };

        $command = new CreateUser(
            $usersRepository
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }

    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function remove(UUID $uuid): void
            {
            }
        };
    }
}