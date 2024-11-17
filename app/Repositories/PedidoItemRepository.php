<?php

namespace App\Repositories;

use Common\Repository;
use App\Models\PedidoItem;
use Doctrine\ORM\Cache\Exception\FeatureNotImplemented;
use Provider\Database\DatabaseException;

class PedidoItemRepository extends Repository implements IPedidoItemRepository {

  #[\Override]
  public function create(PedidoItem $item): PedidoItem {
    try {
      $this->entityManager->persist($item);
      $this->entityManager->flush();

      return $item;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function update(PedidoItem $item): PedidoItem {
    try {
      throw new FeatureNotImplemented('Method "update" from "PedidoRepository" not implemented');
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id) {
    try {
      $item = $this->findById($id);

      $this->entityManager->remove($item);
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return PedidoItem[]
   */
  #[\Override]
  public function findMany(): array {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT u FROM App\Models\PedidoItem p')
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return PedidoItem[]
   */

  #[\Override]
  public function findManyByIdPedido(int $id_pedido): array {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT u FROM App\Models\PedidoItem p WHERE id_pedido :id_pedido')
        ->setParameters([
          'id_pedido' => $id_pedido,
        ])
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id): ?PedidoItem {
    try {
      $pedidoItem = $this->entityManager->find(PedidoItem::class, $id);

      return $pedidoItem;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
