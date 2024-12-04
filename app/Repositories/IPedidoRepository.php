<?php

namespace App\Repositories;

use App\Models\Pedido;

interface IPedidoRepository {

  function create(Pedido $pedido): Pedido;
  function update(Pedido $pedido): Pedido;
  function deleteById(int $id);

  /**
   * @return Pedido[]
   */
  function findMany(): array;
  function findById(int $id): ?Pedido;
  function findByDateRange(string $dataInicial, ?string $dataFinal = null): array;
  function findManyByStatus(string $status): array;
}
