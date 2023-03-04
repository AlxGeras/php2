<?php

namespace alxgeras\Php2\UnitTests\Blog\Commands;

use alxgeras\Php2\Blog\Commands\Arguments;
use alxgeras\Php2\Blog\Commands\CreateUserCommand;
use alxgeras\Php2\Blog\Exceptions\ArgumentsException;
use alxgeras\Php2\Blog\Exceptions\CommandException;
use alxgeras\Php2\Blog\Exceptions\UserNotFoundException;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\UsersRepositoryInterface;
use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Person\Name;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    private function makeUsersRepositoryWithNotFoundException(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface
        {
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
        };
    }

    private function makeUserRepositoryWithUserObjectInReturn(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface
        {
            public function save(User $user): void
            {

            }

            public function get(UUID $uuid): User
            {
                return new User(UUID::random(), "Ivan", new Name("Ivan", "Nikitin"));
            }

            public function getByUsername(string $username): User
            {
                return new User(UUID::random(), "Ivan", new Name("Ivan", "Nikitin"));
            }
        };
    }

    /**
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand($this->makeUserRepositoryWithUserObjectInReturn());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('User already exists: Ivan');

        $command->handle(new Arguments([
            'username' => 'Ivan',
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresUsername(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepositoryWithNotFoundException());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: username');

        $command->handle(new Arguments([]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepositoryWithNotFoundException());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepositoryWithNotFoundException());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
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
        };

        $command = new CreateUserCommand($usersRepository);

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }
}