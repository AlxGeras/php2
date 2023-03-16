<?php

namespace alxgeras\php2\UnitTests\Actions\Posts;

use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Exceptions\AuthException;
use alxgeras\php2\Exceptions\UserNotFoundException;
use alxgeras\php2\Http\Actions\Posts\CreatePost;
use alxgeras\php2\Http\Auth\BearerTokenAuthentication;
use alxgeras\php2\Http\Auth\JsonBodyUsernameAuthentication;
use alxgeras\php2\Http\Auth\JsonBodyUuidAuthentication;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Http\SuccessFulResponse;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\AuthTokensRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase
{

    public function testItReturnsSuccessAnswer(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(BearerTokenAuthentication::class);

        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    UUID::random(),
                    'username',
                    bin2hex(random_bytes(40)),
                    new Name('first', 'last'),
                )
            );

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            '{
                "title": "lorem",
                "text": "lorem"
                }'
        );

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            SuccessFulResponse::class,
            $actual
        );
    }

    /**
     * @throws UserNotFoundException
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorIfAuthTokenNotFound(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(BearerTokenAuthentication::class);

        $authenticationStub
            ->method('user')
            ->willThrowException(
                new AuthException('Bad token: ecd72118-ff57-4d53-a550-b504119ee7f2')
            );

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            '{
                "token": "ecd72118-ff57-4d53-a550-b504119ee7f2"
                }'
        );

        $actual = $createPost->handle($request);
        $actual->send();

        $this->assertInstanceOf(
            ErrorResponse::class,
            $actual
        );

        $this->expectOutputString(
            '{"success":false,"reason":"Bad token: ecd72118-ff57-4d53-a550-b504119ee7f2"}'
        );
    }

    public function ArgumentsProvider(): iterable
    {
        return [
            [
                '{"text": "lorem"}',
                'title'
            ],
            [
                '{"title": "lorem"}',
                'text'
            ]
        ];
    }

    /**
     * @dataProvider ArgumentsProvider
     * @runInSeparateProcess
     * @PreserveGlobalState disabled
     */
    public function testItReturnsErrorIfNotAllParameters(
        $jsonBody,
        $param
    ): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(BearerTokenAuthentication::class);

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            $jsonBody
        );

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            ErrorResponse::class,
            $actual
        );

        $this->expectOutputString(
            '{"success":false,"reason":"No such field: ' . $param . '"}'
        );

        $actual->send();
    }
}