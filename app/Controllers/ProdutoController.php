<?php

namespace App\Controllers;

use App\Repositories\CategoriaRepository;
use App\Services\CategoriaService;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use Core\Enum\StatusCodeHTTP;
use Core\HTTP\Request;
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\ProdutoRepository;
use App\Services\ProdutoService;

#[Controller('/produtos')]
class ProdutoController
{
  private readonly ProdutoService $produtoService;
  private readonly CategoriaService $categoriaService;

  function __construct()
  {
    $this->categoriaService = new CategoriaService(new CategoriaRepository()); // Inicialize a propriedade aqui
    $this->produtoService = new ProdutoService(
      new ProdutoRepository(),
      $this->categoriaService
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request)
  {
    $produtoId = $request->getAttribute('id_produto');

    $result = $this->produtoService->getById([
      'id_produto' => $produtoId,
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
      'nome' => $request->getBody('nome'),
      'valor' => $request->getBody('valor'),
      'descricao' => $request->getBody('descricao'),
      'id_categoria' => $request->getBody('id_categoria'),
      'data_inclusao' => $request->getBody('data_inclusao'),
      'ativo' => $request->getBody('ativo')
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request)
  {
    $produtoId = $request->getAttribute('id_produto');

    $result = $this->produtoService->update([
      'id_produto' => $produtoId,
      'nome' => $request->getBody('nome'),
      'valor' => $request->getBody('valor'),
      'descricao' => $request->getBody('descricao'),
      'id_categoria' => $request->getBody('id_categoria'),
      'data_inclusao' => $request->getBody('data_inclusao'),
      'ativo' => $request->getBody('ativo')
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request)
  {
    $produtoId = $request->getAttribute('id_produto');

    $result = $this->produtoService->delete([
      'id_produto' => $produtoId,
    ]);

    return $result;
  }
}
