<?php

namespace App\Repositories;

use App\Models\Produto;

interface IProdutoRepository {

  function create(Produto $produto): Produto;
  function update(Produto $produto): Produto;
  function deleteById(int $id);

  /**
   * @return Produto[]
   */
  function findMany(): array;
  function findById(int $id): ?Produto;

  /**
   * @return Produto[]
   */
  function findManyByIdCategoria(int $id_categoria, bool $ativo = true): array;
  function findByDescription(string $descricao, int $id = 0): ?Produto;
}
