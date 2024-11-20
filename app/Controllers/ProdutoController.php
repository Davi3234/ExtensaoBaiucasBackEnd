<?php

namespace App\Controllers;

use App\Repositories\CategoriaRepository;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use Core\Enum\StatusCodeHTTP;
use Core\HTTP\Request;
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\ProdutoRepository;
use App\Services\ProdutoService;

#[Controller('/products')]
class ProdutoController
{
  private readonly ProdutoService $produtoService;

  function __construct()
  {
    $this->produtoService = new ProdutoService(
      new ProdutoRepository(),
      new CategoriaRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request)
  {
    $id = $request->getParam('id');

    $result = $this->produtoService->getById([
      'id' => $id
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request)
  {
    $result = $this->produtoService->query();

    return $result;
  }

  #[Post('/', StatusCodeHTTP::CREATED->value)]
  function create(Request $request)
  {
    $result = $this->produtoService->create([
      'nome' => $request->getBody('name'),
      'valor' => $request->getBody('value'),
      'descricao' => $request->getBody('description'),
      'id_categoria' => $request->getBody('category')['id'],
      'data_inclusao' => $request->getBody('data_inclusao'),
      'ativo' => $request->getBody('ativo')
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request)
  {
    $id = $request->getParam('id');

    $result = $this->produtoService->update([
      'id' => $id,
      'nome' => $request->getBody('name'),
      'valor' => $request->getBody('value'),
      'descricao' => $request->getBody('description'),
      'id_categoria' => $request->getBody('category')['id'],
      'data_inclusao' => $request->getBody('data_inclusao'),
      'ativo' => $request->getBody('ativo')
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request)
  {
    $id = $request->getParam('id');

    $result = $this->produtoService->delete([
      'id' => $id,
    ]);

    return $result;
  }
}
