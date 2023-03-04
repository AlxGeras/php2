<?php

namespace alxgeras\Php2\Blog\Commands;

use alxgeras\Php2\Blog\Comment;
use alxgeras\Php2\Blog\Exceptions\ArgumentsException;
use alxgeras\Php2\Blog\Repositories\RepositoryInterfaces\CommentsRepositoryInterface;
use alxgeras\Php2\Blog\UUID;

class CreateCommentCommand
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {

    }

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Сохраняем пользователя в репозиторий
        $this->commentsRepository->save(new Comment(
            UUID::random(),
            UUID::random(),
            UUID::random(),
            $arguments->get('text')
        ));
    }
}