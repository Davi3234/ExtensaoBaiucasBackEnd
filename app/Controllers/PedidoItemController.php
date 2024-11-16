<?php

namespace App\Controllers;

use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\PedidoItemRepository;
use App\Services\PedidoItemService;

#[Controller('/item')]
class PedidoItemController
{

    private readonly PedidoItemService $pedidoItemService;

    #[Get('/')]
    #[Guard(AuthenticationMiddleware::class)]
    function getOne(Request $request)
    {
        $id_item = $request->getAttribute('id_item');
        $id_pedido = $request->getAttribute('id_pedido');

        $result = $this->pedidoItemService->getById([
            'id_item' => $id_item,
            'id_pedido' => $id_pedido
        ]);

        return $result;
    }

    #[Post('/create')]
    function create(Request $request)
    {
        $result = $this->pedidoItemService->create([
            'id_pedido' => $request->getBody('id_pedido'),
            'id_item'   => $request->getBody('id_item'),
        ]);

        return $result;
    }

    #[Put('/')]
    #[Guard(AuthenticationMiddleware::class)]
    function update(Request $request)
    {
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
    function delete(Request $request)
    {
        $id_item = $request->getAttribute('id_item');
        $id_pedido = $request->getAttribute('id_pedido');

        $result = $this->pedidoItemService->delete([
            'id_item' => $id_item,
            'id_pedido' => $id_pedido
        ]);

        return $result;
    }
}
