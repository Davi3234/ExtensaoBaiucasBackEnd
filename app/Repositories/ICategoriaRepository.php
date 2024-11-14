<?php

namespace App\Repositories;

use App\Models\Categoria;

interface ICategoriaRepository {

  function create(Categoria $categoria): Categoria;
  function update(Categoria $categoria): Categoria;
  function deleteById(int $id_categoria);

  /**
   * @return Categoria[]
   */
  function findMany(): array;
  function findById(int $id_categoria): ? Categoria;
}
