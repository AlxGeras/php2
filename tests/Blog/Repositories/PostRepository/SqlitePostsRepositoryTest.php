<?php

namespace alxgeras\Php2\UnitTests\Blog\Repositories\PostRepository;

use alxgeras\Php2\Blog\Exceptions\InvalidArgumentException;
use alxgeras\Php2\Blog\Exceptions\PostNotFoundException;
use alxgeras\Php2\Blog\Post;
use alxgeras\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use alxgeras\Php2\Blog\UUID;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot get post: Мой дом');

        $repository->getByTitle('Мой дом');
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    public function testItReturnPostObjectByTitle(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':title' => 'Мой дом',
            ]);

        $connectionMock->method('prepare')->willReturn($statementMock);

        $statementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
                'title' => 'Мой дом',
                'text' => 'Это мой рандомнй текст',
            ]);

        $repository = new SqlitePostsRepository($connectionMock);

        $result = $repository->getByTitle('Мой дом');

        $this->assertEquals(new Post(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
            'Мой дом',
            'Это мой рандомнй текст'
        ), $result);

        $this->assertEquals(
            '123e4567-e89b-12d3-a456-426614174000 пишет: Мой дом' . PHP_EOL . 'Это мой рандомнй текст',
            (string)$result
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    public function testItReturnPostObjectByUuid(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ]);

        $connectionMock->method('prepare')->willReturn($statementMock);

        $statementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
                'title' => 'Мой дом',
                'text' => 'Это мой рандомнй текст',
            ]);

        $repository = new SqlitePostsRepository($connectionMock);

        $result = $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));

        $this->assertEquals(new Post(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
            'Мой дом',
            'Это мой рандомнй текст'
        ), $result);

        $this->assertEquals(
            '123e4567-e89b-12d3-a456-426614174000 пишет: Мой дом' . PHP_EOL . 'Это мой рандомнй текст',
            (string)$result
        );
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '9de6281b-6fa3-427b-b071-4ca519586e74',
                ':title' => 'Мой дом',
                ':text' => 'Это мой рандомнй текст',
            ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub);

        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new UUID('9de6281b-6fa3-427b-b071-4ca519586e74'),
                'Мой дом',
                'Это мой рандомнй текст'
            )
        );
    }
}