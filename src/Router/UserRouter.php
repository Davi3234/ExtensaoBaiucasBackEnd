<?php

namespace App\Router;

use App\Core\Components\Router;
use App\Controller\UserController;
use App\Middleware\AuthenticationMiddleware;

$router = Router::maker('/users');

$router->get('', AuthenticationMiddleware::class, [UserController::class, 'query']);
$router->get('/:id', [UserController::class, 'getOne']);
$router->post('/create', [UserController::class, 'create']);
$router->put('/:id/update', AuthenticationMiddleware::class, [UserController::class, 'update']);
$router->delete('/:id', AuthenticationMiddleware::class, [UserController::class, 'delete']);
