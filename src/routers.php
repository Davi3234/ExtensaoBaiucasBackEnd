<?php

use App\Controller\UserController;
use App\Core\Components\Router;

Router::get('/hello', [UserController::class, 'hello']);
