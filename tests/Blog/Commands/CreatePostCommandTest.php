<?php

namespace alxgeras\Php2\UnitTests\Blog\Commands;

use alxgeras\Php2\Blog\Commands\Arguments;
use alxgeras\Php2\Blog\Commands\CreatePostCommand;
use alxgeras\Php2\Blog\Post;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\PostsRepositoryInterface;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\UsersRepositoryInterface;
use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\ArgumentsException;
use alxgeras\Php2\Exceptions\CommandException;
use alxgeras\Php2\Exceptions\InvalidArgumentException;
use alxgeras\Php2\Exceptions\PostNotFoundException;
use alxgeras\Php2\Person\Name;
use PHPUnit\Framework\TestCase;

class CreatePostCommandTest extends TestCase
{
    private function makePostRepositoryWithNotFoundException(): PostsRepositoryInterface
    {
        return new class implements PostsRepositoryInterface
        {
            public function save(Post $post): void
            {

            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException("Not found");
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException("Not found");
            }

            public function delete(UUID $uuid): void
            {
            }
        };
    }

    private function makePostRepositoryWithPostObjectInReturn(): PostsRepositoryInterface
    {
        return new class implements PostsRepositoryInterface
        {
            public function save(Post $post): void
            {

            }

            public function get(UUID $uuid): Post
            {
                $user = new User(
                    new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
                    'username',
                    new Name('firstname', 'lastname')
                );

                return new Post(
                    new UUID('123e4567-e89b-12d3-a456-426614174000'),
                    $user,
                    'Мой дом',
                    'Это мой рандомнй текст'
                );
            }

            public function getByTitle(string $title): Post
            {
                $user = new User(
                    new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
                    'username',
                    new Name('firstname', 'lastname')
                );

                return new Post(
                    new UUID('123e4567-e89b-12d3-a456-426614174000'),
                    $user,
                    'Мой дом',
                    'Это мой рандомнй текст'
                );
            }

            public function delete(UUID $uuid): void
            {
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
                return new User(
                    new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
                    'username',
                    new Name('firstname', 'lastname')
                );
            }

            public function getByUsername(string $title): User
            {
                return new User(
                    new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
                    'username',
                    new Name('firstname', 'lastname')
                );
            }
        };
    }

    /**
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenPostAlreadyExists(): void
    {
        $command = new CreatePostCommand([
            'posts_repository' => $this->makePostRepositoryWithPostObjectInReturn(),
            'users_repository' => $this->makeUserRepositoryWithUserObjectInReturn()
        ]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Post already exists: Мой дом');

        $command->handle(new Arguments([
            'author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
            'title' => 'Мой дом',
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresAuthorUuid(): void
    {
        $command = new CreatePostCommand([
            'posts_repository' => $this->makePostRepositoryWithNotFoundException(),
            'users_repository' => $this->makeUserRepositoryWithUserObjectInReturn()
        ]);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: title');

        $command->handle(new Arguments([

        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresTitle(): void
    {
        $command = new CreatePostCommand([
            'posts_repository' => $this->makePostRepositoryWithNotFoundException(),
            'users_repository' => $this->makeUserRepositoryWithUserObjectInReturn()
        ]);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: title');

        $command->handle(new Arguments([
            'author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
        ]));
    }

    /**
     * @throws CommandException
     */
    public function testItRequiresText(): void
    {
        $command = new CreatePostCommand([
            'posts_repository' => $this->makePostRepositoryWithNotFoundException(),
            'users_repository' => $this->makeUserRepositoryWithUserObjectInReturn()
        ]);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: text');

        $command->handle(new Arguments([
            'author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
            'title' => 'Мой дом',
        ]));
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException|InvalidArgumentException
     */
    public function testItSavesUserToRepository(): void
    {
        $postsRepository = new class implements PostsRepositoryInterface {

            private bool $called = false;

            public function save(Post $post): void
            {
                $this->called = true;
            }
            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException("Not found");
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }

            public function delete(UUID $uuid): void
            {
            }
        };

        $command = new CreatePostCommand([
            'posts_repository' => $postsRepository,
            'users_repository' => $this->makeUserRepositoryWithUserObjectInReturn()]);

        $command->handle(new Arguments([
            'author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
            'title' => 'Мой дом',
            'text' => 'Это мой рандомнй текст',
        ]));

        $this->assertTrue($postsRepository->wasCalled());
    }
}