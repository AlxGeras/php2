<?php

namespace alxgeras\Php2\UnitTests\Actions\Posts;

use alxgeras\Php2\Actions\Posts\DeletePost;
use alxgeras\Php2\Blog\Post;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\PostsRepositoryInterface;
use alxgeras\Php2\Blog\User;
use alxgeras\Php2\Blog\UUID;
use alxgeras\Php2\Exceptions\PostNotFoundException;
use alxgeras\Php2\http\ErrorResponse;
use alxgeras\Php2\http\Request;
use alxgeras\Php2\http\SuccessfulResponse;
use alxgeras\Php2\Person\Name;
use PHPUnit\Framework\TestCase;
use JsonException;

class DeletePostActionTest extends TestCase
{
    private function postsRepository(array $posts): PostsRepositoryInterface
    {
        return new class($posts) implements PostsRepositoryInterface
        {
            private bool $called = false;

            public function __construct(
                private array $posts
            )
            {
            }

            public function save(Post $post): void
            {
            }

            public function get(UUID $uuid): Post
            {
                foreach ($this->posts as $post) {
                    if ($post instanceof Post && (string)$uuid === $post->getUuid()) {
                        return $post;
                    }
                }
                throw new PostNotFoundException('Not found');
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function delete(UUID $uuid): void
            {
                $this->called = true;
            }

            public function getCalled(): bool
            {
                return $this->called;
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsErrorResponseIfCannotDecodeJsonBody(): void
    {
        $request = new Request([], [], "");

        $postsRepository = $this->postsRepository([]);

        $action = new DeletePost($postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Cannot decode json body"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsErrorResponseIfNoUuidProvided(): void
    {
        $request = new Request([], [], '{"wr":"2342342"}');

        $postsRepository = $this->postsRepository([]);

        $action = new DeletePost($postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such Field: uuid"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsErrorResponseIfNotFoundPost(): void
    {
        $request = new Request([], [], '{"uuid":"10373537-0805-4d7a-830e-22b481b4859c"}');

        $postsRepository = $this->postsRepository([]);

        $action = new DeletePost($postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"uuid":"10373537-0805-4d7a-830e-22b481b4859c"}');

        $user = new User(
            new UUID('10373537-0805-4d7a-830e-22b481b4859c'),
            'username',
            new Name('name', 'surname')
        );

        $postsRepository = $this->postsRepository([
            new Post(
                new UUID('10373537-0805-4d7a-830e-22b481b4859c'),
                $user,
                'title',
                'text'
            )
        ]);

        $action = new DeletePost($postsRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"uuid":"10373537-0805-4d7a-830e-22b481b4859c"}}');

        $response->send();
    }
}