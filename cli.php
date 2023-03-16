<?php

use alxgeras\php2\Blog\Commands\DeletePost;
use alxgeras\php2\Blog\Commands\FakeData\PopulateDB;
use alxgeras\php2\Blog\Commands\Users\CreateUser;
use alxgeras\php2\Blog\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandsClass) {
    $command = $container->get($commandsClass);
    $application->add($command);
}

$application->run();
