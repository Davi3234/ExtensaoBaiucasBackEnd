<?php

namespace App\Controllers;

use App\Repositories\ProdutoRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\PedidoItemRepository;
use App\Repositories\PedidoRepository;
use App\Services\PedidoItemService;
use App\Services\PedidoService;

#[Controller('/pedido')]
class PedidoController
{
  private readonly PedidoService $pedidoService;
  private readonly UserService $userService;

  function __construct()
  {
    $this->userService = new UserService(new UserRepository());
    $this->pedidoService = new PedidoService(
      new PedidoRepository(),
      $this->userService,
      new PedidoItemService(
        new PedidoItemRepository(),
        new ProdutoRepository(),
        new PedidoRepository()
      )
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request)
  {
    $pedido_id = $request->getAttribute('id');

    $result = $this->pedidoService->getById([
      'id' => $pedido_id
    ]);

    return $result;
  }

  #[Post('/create')]
  function create(Request $request)
  {
    $result = $this->pedidoService->create([
      'id' => $request->getBody('id'),
      'id_cliente' => $request->getBody('id_cliente'),
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request)
  {
    $id = $request->getAttribute('id');

    $result = $this->pedidoService->update([
      'id' => $id,
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request)
  {
    $id = $request->getAttribute('id');

    $result = $this->pedidoService->delete([
      'id' => $id,
    ]);

    return $result;
  }
}
