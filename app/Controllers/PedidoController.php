<?php

namespace App\Controllers;

use App\Repositories\ProdutoRepository;
use App\Repositories\UserRepository;
use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\PedidoItemRepository;
use App\Repositories\PedidoRepository;
use App\Services\PedidoItemService;
use App\Services\PedidoService;

#[Controller('/orders')]
class PedidoController
{
  private readonly PedidoService $pedidoService;

  function __construct()
  {
    $this->pedidoService = new PedidoService(
      new PedidoRepository(),
      new PedidoItemService(
        new PedidoItemRepository(),
        new ProdutoRepository(),
        new PedidoRepository()
      ),
      new UserRepository(),
      new ProdutoRepository()

    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request)
  {
    $pedido_id = $request->getParam('id');

    $result = $this->pedidoService->getById([
      'id' => $pedido_id
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request)
  {
    $result = $this->pedidoService->query();

    return $result;
  }

  #[Post('/')]
  function create(Request $request)
  {
    $result = $this->pedidoService->create([
      'id_cliente' => $request->getBody('cliente')['id_cliente'],
      'data_pedido' => $request->getBody('data_pedido'),
      'status' => $request->getBody('status'),
      'observacoes' => $request->getBody('observacoes'),
      'forma_pagamento' => $request->getBody('forma_pagamento'),
      'tipo_entrega' => $request->getBody('tipo'),
      'endereco_entrega' => $request->getBody('endereco_entrega'),
      'taxa_entrega' => $request->getBody('taxa_entrega'),
      'itens' => $request->getBody('itens'),
      'id_produto' => $request->getBody('id_produto'),
      'observacoes_item' => $request->getBody('observacoes_item')
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request)
  {
    $pedido_id = $request->getParam('id');

    $result = $this->pedidoService->update([
      'id' => $pedido_id,
      'id_cliente' => $request->getBody('cliente')['id_cliente'],
      'data_pedido' => $request->getBody('data_pedido'),
      'status' => $request->getBody('status'),
      'observacoes' => $request->getBody('observacoes'),
      'forma_pagamento' => $request->getBody('forma_pagamento'),
      'tipo_entrega' => $request->getBody('tipo'),
      'endereco_entrega' => $request->getBody('endereco_entrega'),
      'taxa_entrega' => $request->getBody('taxa_entrega'),
      'id_produto' => $request->getBody('id_produto'),
      'observacoes_item' => $request->getBody('observacoes_item'),
      'valor_item' => $request->getBody('valor_item')
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request)
  {
    $id = $request->getParam('id');

    $result = $this->pedidoService->delete([
      'id' => $id,
    ]);

    return $result;
  }
}
