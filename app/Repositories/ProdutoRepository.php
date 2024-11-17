<?php

namespace App\Repositories;

use Common\Repository;
use App\Models\Produto;
use Doctrine\ORM\Cache\Exception\FeatureNotImplemented;
use Provider\Database\DatabaseException;

class ProdutoRepository extends Repository implements IProdutoRepository {

  #[\Override]
  public function create(Produto $produto): Produto {
    try {
      $this->entityManager->persist($produto);
      $this->entityManager->flush();

      return $produto;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function update(Produto $produto): Produto {
    try {
      throw new FeatureNotImplemented('Method "update" from "ProdutoRepository" not implemented');
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id_produto) {
    try {
      $produto = $this->findById($id_produto);

      $this->entityManager->remove($produto);
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return Produto[]
   */
  #[\Override]
  public function findMany(): array {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT p FROM App\Models\Produto p')
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id_produto): ?Produto {
    try {
      $produto = $this->entityManager->find(Produto::class, $id_produto);

      return $produto;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
