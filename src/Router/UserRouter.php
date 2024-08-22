<?php

namespace App\Router;

use App\Server\Components\Router;
use App\Controller\UserController;

$router = Router::maker('/users');

$router->post('/create', [UserController::class, 'create']);
