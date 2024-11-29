<?php

namespace App\Repositories;

use Common\Repository;
use App\Models\Pedido;
use Doctrine\ORM\Cache\Exception\FeatureNotImplemented;
use Provider\Database\DatabaseException;

class PedidoRepository extends Repository implements IPedidoRepository
{

  #[\Override]
  public function create(Pedido $pedido): Pedido
  {
    try {
      $this->entityManager->persist($pedido);
      $this->entityManager->flush();

      return $pedido;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function update(Pedido $pedido): Pedido
  {
    try {
      $this->entityManager->persist($pedido);
      $this->entityManager->flush();

      return $pedido;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id)
  {
    try {
      $pedido = $this->findById($id);

      $this->entityManager->remove($pedido);
      $this->entityManager->flush();
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return Pedido[]
   */

  #[\Override]
  public function findMany(): array
  {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT p FROM App\Models\Pedido p')
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id): ?Pedido
  {
    try {
      $pedido = $this->entityManager->find(Pedido::class, $id);

      return $pedido;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return Pedido[]
   */
  public function findManyByStatus(string $statusPedido): array
  {
    try {
      return $this->entityManager
        ->createQuery('SELECT p FROM App\Models\Pedido p WHERE p.status = :statusPedido')
        ->setParameter('statusPedido', $statusPedido)
        ->getResult();
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }



  public function findByDateRange(string $dataInicial, ?string $dataFinal = null): array
  {
    try {
      if ($dataFinal) {
        return $this->entityManager

          ->createQuery('SELECT p FROM App\Models\Pedido p WHERE p.data_pedido >= :dataInicial and p.data_pedido <= :dataFinal')
          ->setParameter('dataInicial', $dataInicial)
          ->setParameter('dataFinal', $dataFinal)
          ->getResult();
      } else {
        return $this->entityManager

          ->createQuery('SELECT p FROM App\Models\Pedido p WHERE p.data_pedido >= :dataInicial')
          ->setParameter('dataInicial', $dataInicial)
          ->getResult();
      }
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
