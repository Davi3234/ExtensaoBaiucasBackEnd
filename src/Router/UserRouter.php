<?php

namespace App\User\Router;

use App\User\Controller\UserController;
use App\Core\Components\Router;

Router::post('/create', [UserController::class, 'create']);