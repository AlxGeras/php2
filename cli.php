<?php

use alxgeras\php2\Blog\{UUID};
use alxgeras\php2\Exceptions\AppException;
use alxgeras\php2\Blog\Commands\CreateUserCommand;
use alxgeras\php2\Blog\Commands\Arguments;

$faker = Faker\Factory::create('ru_RU');

$container = require __DIR__ . '/bootstrap.php';

try {
    $command = $container->get(CreateUserCommand::class);
    $command->hanle(Arguments::fromArgv($argv));

} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}
