<?php

namespace App\Controllers;

use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Services\PedidoItemService;

#[Controller('/item')]
class PedidoItemController {

    private readonly PedidoItemService $pedidoItemService;

    #[Get('/:id')]
    #[Guard(AuthenticationMiddleware::class)]
    function getOne(Request $request) {
        $result = $this->pedidoItemService->getById([
            'id' => $request->getParam('id'),
        ]);

        return $result;
    }

    #[Post('/create')]
    function create(Request $request) {
        $result = $this->pedidoItemService->create([
            'id_pedido' => $request->getBody('id_pedido'),
            'id_item'   => $request->getBody('id_item'),
            'valor_item' => $request->getBody('valor_item'),
            'observacoes_item'   => $request->getBody('observacoes_item'),
        ]);

        return $result;
    }

    #[Put('/:id')]
    #[Guard(AuthenticationMiddleware::class)]
    function update(Request $request) {
        $result = $this->pedidoItemService->update([
            'id' => $request->getParam('id'),
            'valor_item' => $request->getBody('valor_item'),
            'observacoes_item' => $request->getBody('observacoes_item'),
        ]);

        return $result;
    }

    #[Delete('/:id')]
    #[Guard(AuthenticationMiddleware::class)]
    function delete(Request $request) {
        $result = $this->pedidoItemService->delete([
            'id' => $request->getAttribute('id'),
        ]);

        return $result;
    }
}
