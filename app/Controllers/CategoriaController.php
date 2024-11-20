<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\CategoriaRepository;
use App\Repositories\ProdutoRepository;
use App\Services\CategoriaService;
use Core\Enum\StatusCodeHTTP;

#[Controller('/categories')]
class CategoriaController {
  private readonly CategoriaService $categoriaService;

  function __construct() {
    $this->categoriaService = new CategoriaService(
      new CategoriaRepository(),
      new ProdutoRepository(),
      new UserRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $categoria_id = $request->getParam('id');

    $result = $this->categoriaService->getById([
      'id' => $categoria_id
    ]);


    return $result;
  }

  #[Get('/products')]
  #[Guard(AuthenticationMiddleware::class)]
  function getManyProducts(Request $request) {
    $result = $this->categoriaService->queryProdutos([
      'userId' => $request->getAttribute('userId')
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request) {
    $result = $this->categoriaService->query();

    return $result;
  }

  #[Post('/', StatusCodeHTTP::CREATED->value)]
  function create(Request $request) {
    $result = $this->categoriaService->create([
      'descricao' => $request->getBody('name')
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $id = $request->getParam('id');
    $descricao = $request->getBody('name');

    $result = $this->categoriaService->update([
      'id' => $id,
      'descricao' => $descricao
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $id = $request->getParam('id');

    $result = $this->categoriaService->delete([
      'id' => $id,
    ]);

    return $result;
  }
}
