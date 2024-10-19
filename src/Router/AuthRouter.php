<?php

namespace App\Router;

use App\Core\Components\Router;
use App\Controller\AuthController;

$router = Router::maker('/auth');

$router->post('/login', [AuthController::class, 'login']);
