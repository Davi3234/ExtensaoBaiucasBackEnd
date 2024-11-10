<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\PedidoController;
use App\Controllers\PedidoItemController;
use App\Controllers\ProdutoController;

return [
  'controllers' => [
    AuthController::class,
    UserController::class,
    PedidoController::class,
    PedidoItemController::class,
    ProdutoController::class
  ]
];
