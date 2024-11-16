<?php

namespace App\Controllers;

use Core\HTTP\Request;
use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\CategoriaRepository;
use App\Services\CategoriaService;

#[Controller('/categoria')]
class CategoriaController {
  private readonly CategoriaService $categoriaService;

  function __construct() {
    $this->categoriaService = new CategoriaService(
      new CategoriaRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $categoria_id = $request->getAttribute('id_categoria');

    $result = $this->categoriaService->getById([
      'id_categoria' => $categoria_id
    ]);

    return $result;
  }

  #[Post('/create')]
  #[Guard(AuthenticationMiddleware::class)]
  function create(Request $request) {
    $result = $this->categoriaService->create([
      'id_categoria' => $request->getBody('id_categoria')
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $id_categoria = $request->getAttribute('id_categoria');

    $result = $this->categoriaService->update([
      'id_categoria' => $id_categoria,
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $id_categoria = $request->getAttribute('id_categoria');

    $result = $this->categoriaService->delete([
      'id_categoria' => $id_categoria,
    ]);

    return $result;
  }
}
