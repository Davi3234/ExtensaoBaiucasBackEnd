<?php

namespace App\Controllers;

use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\CategoriaRepository;
use App\Services\CategoriaService;
use Core\HTTP\RouterURL;


#[Controller('/categoria')]
class CategoriaController
{
  private readonly CategoriaService $categoriaService;

  function __construct()
  {
    $this->categoriaService = new CategoriaService(
      new CategoriaRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request)
  {
    $categoria_id = $request->getParam('id');

    $result = $this->categoriaService->getById([
      'id' => $categoria_id
    ]);


    return $result;
  }

  #[Post('/create')]
  #[Guard(AuthenticationMiddleware::class)]
  function create(Request $request)
  {
    $result = $this->categoriaService->create([
      'id' => $request->getBody('id'),
      'descricao' => $request->getBody('descricao')
    ]);

    return $result;
  }

  #[Put('/update')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request)
  {
    $id = $request->getBody('id');
    $descricao = $request->getBody('descricao');

    $result = $this->categoriaService->update([
      'id' => $id,
      'descricao' => $descricao
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request)
  {
    $id = $request->getBody('id');

    $result = $this->categoriaService->delete([
      'id' => $id,
    ]);

    return $result;
  }
}
