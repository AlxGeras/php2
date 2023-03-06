<?php

namespace alxgeras\php2\UnitTests\CommentsRepositoryTests;

use alxgeras\php2\Blog\Comment;
use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\CommentNotFoundException;
use alxgeras\php2\Exceptions\InvalidUuidException;
use alxgeras\php2\Exceptions\PostNotFoundException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\CommentsRepository\SqliteCommentsRepository;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentsToDatabase(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
                ':txt' => 'Из предыдущей главы уже видно',
                ':user_uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
                ':post_uuid' => '7bd053ac-6dfb-46ac-908a-35222a579851'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub);

        $user = new User(
            new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'),
            new Name('first', 'last'),
            'username'
        );

        $post = new Post(
            new UUID('7bd053ac-6dfb-46ac-908a-35222a579851'),
            $user,
            'Пожалуй, я',
            'Из предыдущей главы уже видно'
        );

        $comment = new Comment(
            new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'),
            $user,
            $post,
            'Из предыдущей главы уже видно'
        );

        $commentRepository->save($comment);
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock->method('fetch')->willReturn([
            'user_uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
            'first_name' => 'first',
            'last_name' => 'last',
            'username' => 'username',
            'post_uuid' => '10418021-7cc6-4221-a1e8-29bfac1b4d20',
            'title' => 'title',
            'text' => 'text',
            'txt' => 'txt'
        ]);

        $commentRepository = new SqliteCommentsRepository($connectionStub);
        $comment = $commentRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));

        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$comment->getUuid());
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function testItThrowAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Комментария с uuid: 9dba7ab0-93be-4ff4-9699-165320c97694 нет');

        $commentRepository = new SqliteCommentsRepository($connectionStub);
        $commentRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
    }
}