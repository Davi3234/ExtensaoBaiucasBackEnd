<?php

namespace App\User\Router;

use App\Core\Components\Router;
use App\User\Controller\UserController;

$router = Router::maker('/users');

$router->post('/create', [UserController::class, 'create']);