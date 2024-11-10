<?php

namespace App\Controllers;

use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
//use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\PedidoRepository;
use App\Repositories\PedidoItemRepository;
use App\Services\PedidoService;
use App\Services\PedidoItemService;

//Pedido:
#[Controller('/pedido')]
class PedidoController {
  private readonly PedidoService $pedidoService;

  function __construct() {
    $this->pedidoService = new pedidoService(
      new pedidoRepository()
    );
  }

  #[Get('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $pedido_id = $request->getAttribute('id_pedido');

    $result = $this->pedidoService->getById([
      'id_pedido' => $pedido_id
    ]);

    return $result;
  }

  #[Post('/create')]
  function create(Request $request) {
    $result = $this->pedidoService->create([
      'id_pedido' => $request->getBody('id_pedido'),
      'id_cliente' => $request->getBody('id_cliente'),
    ]);

    return $result;
  }

  #[Put('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $id_pedido = $request->getAttribute('id_pedido');

    $result = $this->pedidoService->update([
      'id_pedido' => $id_pedido,
    ]);

    return $result;
  }

  #[Delete('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $id_pedido = $request->getAttribute('id_pedido');

    $result = $this->pedidoService->delete([
      'id_pedido' => $id_pedido,
    ]);

    return $result;
  }


////////////////////////////////////////////////////////////////////////////////////////////

//Item:
#[Controller('/item')]
  private readonly PedidoItemService $pedidoItemService;

  function __construct() {
    $this->pedidoItemService = new pedidoItemService(
      new pedidoItemRepository()
    );
  }

  #[Get('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $id_item = $request->getAttribute('id_item');
    $id_pedido = $request->getAttribute('id_pedido');

    $result = $this->pedidoItemService->getById([
      'id_item' => $id_item
      'id_pedido' => $id_pedido
    ]);

    return $result;
  }

  #[Post('/create')]
  function create(Request $request) {
    $result = $this->pedidoItemService->create([
      'id_pedido' => $request->getBody('id_pedido'),
      'id_item'   => $request->getBody('id_item'),
    ]);

    return $result;
  }

  #[Put('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $id_item = $request->getAttribute('id_item');
    $id_pedido = $request->getAttribute('id_pedido');

    $result = $this->pedidoItemService->update([
      'id_item' => $id_item,
      'id_pedido' => $id_pedido
    ]);

    return $result;
  }

  #[Delete('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $id_item = $request->getAttribute('id_item');
    $id_pedido = $request->getAttribute('id_pedido');

    $result = $this->pedidoItemService->delete([
      'id_item' => $id_item,
      'id_pedido' => $id_pedido
    ]);

    return $result;
  }
}
