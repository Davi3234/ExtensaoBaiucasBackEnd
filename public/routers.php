<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\PedidoController;
use App\Controllers\PedidoItemController;

return [
  'controllers' => [
    AuthController::class,
    UserController::class,
    PedidoController::class,
    PedidoItemController::class
  ]
];
