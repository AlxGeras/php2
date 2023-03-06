<?php

use alxgeras\php2\Http\Actions\Users\FindByUsername;
use alxgeras\php2\Http\Actions\Posts\FindByUuidPost;
use alxgeras\php2\Http\Actions\Posts\CreatePost;
use alxgeras\php2\Http\Request;
use alxgeras\php2\Exceptions\HttpException;
use alxgeras\php2\Http\ErrorResponse;
use alxgeras\php2\Http\Actions\Comments\CreateComment;
use alxgeras\php2\Http\Actions\Posts\RemovePost;
use alxgeras\php2\Http\Actions\Comments\FindByUuidComment;
use alxgeras\php2\Http\Actions\Comments\RemoveComment;
use alxgeras\php2\Http\Actions\Likes\CreatePostLike;
use alxgeras\php2\Http\Actions\Likes\RemovePostLike;
use alxgeras\php2\Http\Actions\Likes\CreateCommentLike;
use alxgeras\php2\Http\Actions\Likes\RemoveCommentLike;

$container = require __DIR__ . '/bootstrap.php';

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuidPost::class,
        '/comments/show' => FindByUuidComment::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/postLikes/create' => CreatePostLike::class,
        '/commentLikes/create' => CreateCommentLike::class,
    ],
    'DELETE' => [
        '/posts' => RemovePost::class,
        '/comments' => RemoveComment::class,
        '/postLikes' => RemovePostLike::class,
        '/commentLikes' => RemoveCommentLike::class,
    ],
];

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $method = $request->method();
    $path = $request->path();
} catch (HttpException $e) {
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("$method not found"))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("$path not found"))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

$response = $action->handle($request);

$response->send();

