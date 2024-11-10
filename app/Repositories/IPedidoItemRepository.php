<?php

namespace App\Repositories;

use App\Models\PedidoItem;

interface IPedidoItemRepository {

  function create(ItemPedido $itemPedido): ItemPedido;
  function update(ItemPedido $itemPedido): ItemPedido;
  function deleteById(int $id_item);

  /**
   * @return ItemPedido[]
   */
  function findMany(): array;
  function findById(int $id_item): ? idItemPedido;
}
