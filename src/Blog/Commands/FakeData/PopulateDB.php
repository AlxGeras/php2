<?php

namespace alxgeras\php2\Blog\Commands\FakeData;

use alxgeras\php2\Blog\Comment;
use alxgeras\php2\Blog\Post;
use alxgeras\php2\Blog\User;
use alxgeras\php2\Blog\UUID;
use alxgeras\php2\Person\Name;
use alxgeras\php2\Repositories\Interfaces\CommentsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\PostsRepositoryInterface;
use alxgeras\php2\Repositories\Interfaces\UsersRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator            $faker,
        private UsersRepositoryInterface    $usersRepository,
        private PostsRepositoryInterface    $postsRepository,
        private CommentsRepositoryInterface $commentsRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Regulates the number of fake users'
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Regulates the number of fake posts'
            )
            ->addOption(
                'comments-number',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Regulates the number of fake comments'
            );
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int
    {
        $usersNumber = $input->getOption('users-number') ?? 10;

        $postsNumber = $input->getOption('posts-number') ?? 15;

        $commentsNumber = $input->getOption('comments-number') ?? 5;

        $users = [];
        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUsername());
        }

        foreach ($users as $user) {
            for ($i = 0; $i < $postsNumber; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());

                for ($j = 0; $j < $commentsNumber; $j++) {
                    $userKey = array_rand($users);
                    $comment = $this->createFakeComment($users[$userKey], $post);
                    $output->writeln('Comment created: ' . $comment->getUuid());
                }

            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            $this->faker->password,
            new Name(
                $this->faker->firstName,
                $this->faker->lastName
            )
        );

        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(User $user, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $user,
            $post,
            $this->faker->realText
        );

        $this->commentsRepository->save($comment);
        return $comment;
    }
}