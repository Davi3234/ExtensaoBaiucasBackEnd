<?php

namespace App\Router;

use App\Core\Components\Router;
use App\Controller\UserController;
use App\Middleware\AuthenticationMiddleware;

// 1
$router = Router::maker('/users');
$router->get('/create', [UserController::class, 'create']);
$router->get('/:id/update', AuthenticationMiddleware::class, [UserController::class, 'update']);

// 2
// Router::get('/users/create', [UserController::class, 'create']);
