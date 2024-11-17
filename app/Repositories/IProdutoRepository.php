<?php

namespace App\Repositories;

use App\Models\Produto;

interface IProdutoRepository {

  function create(Produto $produto): Produto;
  function update(Produto $produto): Produto;
  function deleteById(int $id_produto);

  /**
   * @return Produto[]
   */
  function findMany(): array;
  function findById(int $id_produto): ?Produto;
}
