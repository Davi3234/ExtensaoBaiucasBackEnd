<?php

namespace App\Repositories;

use App\Models\PedidoItem;

interface IPedidoItemRepository {

  function create(PedidoItem $itemPedido): PedidoItem;
  function update(PedidoItem $itemPedido): PedidoItem;
  function deleteById(int $id);

  /**
   * @return PedidoItem[]
   */
  function findMany(): array;

  /**
   * @return PedidoItem[]
   */
  function findManyByIdPedido(int $id_pedido): array;
  function findById(int $id): ?PedidoItem;
}
