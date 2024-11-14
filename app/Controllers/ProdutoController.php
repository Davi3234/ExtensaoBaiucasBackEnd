<?php

namespace App\Controllers;

use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use Core\Enum\StatusCodeHTTP;
use Core\HTTP\Request;
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\ProdutoRepository;
use App\Services\ProdutoService;

#[Controller('/produto')]
class ProdutoController {
  private readonly ProdutoService $produtoService;

  function __construct() {
    $this->produtoService = new ProdutoService(
      new ProdutoRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $produtoId = $request->getAttribute('id_produto');

    $result = $this->produtoService->getById([
      'id_produto' => $produtoId,
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request) {
    $result = $this->produtoService->query();

    return $result;
  }

  #[Post('/create', StatusCodeHTTP::CREATED->value)]
  function create(Request $request) {
    $result = $this->produtoService->create([
      'nome' => $request->getBody('nome'),
      'valor' => $request->getBody('valor'),
      'id_categoria' => $request->getBody('id_categoria'),
      'data_inclusao' => $request->getBody('data_inclusao')
    ]);

    return $result;
  }

  #[Put('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $produtoId = $request->getAttribute('id_produto');

    $result = $this->produtoService->update([
      'id_produto' => $produtoId
    ]);

    return $result;
  }

  #[Delete('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $produtoId = $request->getAttribute('id_produto');

    $result = $this->produtoService->delete([
      'id_produto' => $produtoId,
    ]);

    return $result;
  }
}