<?php

namespace App\Router;

use App\Core\Components\Router;
use App\Controller\UserController;
use App\Middleware\AuthenticationMiddleware;

$router = Router::maker('/users');

$router->get('/', [UserController::class, 'query']);
$router->post('/create', [UserController::class, 'create']);
$router->delete('/:id', AuthenticationMiddleware::class, [UserController::class, 'delete']);
$router->get('/:id/update', AuthenticationMiddleware::class, [UserController::class, 'update']);
