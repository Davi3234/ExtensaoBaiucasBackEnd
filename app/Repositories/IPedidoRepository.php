<?php

namespace App\Repositories;

use App\Models\Pedido;

interface IPedidoRepository {

  function create(Pedido $pedido): Pedido;
  function update(Pedido $pedido): Pedido;
  function deleteById(int $id_pedido);

  /**
   * @return Pedido[]
   */
  function findMany(): array;
  function findById(int $id_pedido): ? Pedido;
}
