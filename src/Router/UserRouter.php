<?php

namespace App\Router;

use App\Core\Components\Router;
use App\Controller\UserController;

$router = Router::maker('/users');

$router->get('/create', [UserController::class, 'create']);
$router->get('/:id/update', [UserController::class, 'update']);