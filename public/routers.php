<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\PedidoController;
use App\Controllers\PedidoItemController;
use App\Controllers\ProdutoController;
use App\Controllers\CategoriaController;

return [
  'controllers' => [
    AuthController::class,
    UserController::class,
    PedidoController::class,
    PedidoItemController::class,
    ProdutoController::class,
    CategoriaController::class
  ]
];
