<?php

namespace App\Repositories;

use Common\Repository;
use App\Models\Pedido;
use Doctrine\ORM\Cache\Exception\FeatureNotImplemented;
use Provider\Database\DatabaseException;

class PedidoRepository extends Repository implements IPedidoRepository {

  #[\Override]
  public function create(Pedido $pedido): Pedido {
    try {
      $this->entityManager->persist($pedido);
      $this->entityManager->flush();

      return $pedido;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function update(Pedido $pedido): Pedido {
    try {
      throw new FeatureNotImplemented('Method "update" from "PedidoRepository" not implemented');
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id_pedido) {
    try {
      $pedido = $this->findById($id_pedido);

      $this->entityManager->remove($pedido);
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return Pedido[]
   */

  #[\Override]
  public function findMany(): array {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT u FROM App\Models\Pedido p')
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id_pedido): ? Pedido {
    try {
      $pedido = $this->entityManager->find(Pedido::class, $id_pedido);

      return $pedido;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
