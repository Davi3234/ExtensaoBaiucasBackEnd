<?php

namespace App\Router;

use App\Core\Components\Router;
use App\Controller\UserController;
use App\Middleware\AuthenticationMiddleware;

$router = Router::maker('/users');

$router->get('/create', [UserController::class, 'create']);
$router->get('/:id/update', AuthenticationMiddleware::class, [UserController::class, 'update']);