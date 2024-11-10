<?php

namespace App\Repositories;

use App\Models\PedidoItem;

interface IPedidoItemRepository {

  function create(PedidoItem $itemPedido): PedidoItem;
  function update(PedidoItem $itemPedido): PedidoItem;
  function deleteById(int $id_item);

  /**
   * @return PedidoItem[]
   */
  function findMany(): array;
  function findById(int $id_item): ? PedidoItem;
}
