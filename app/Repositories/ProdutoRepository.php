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
      $this->entityManager->persist($produto);
      $this->entityManager->flush();

      return $produto;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id) {
    try {
      $produto = $this->findById($id);

      $this->entityManager->remove($produto);
      $this->entityManager->flush();
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

  /**
   * @return Produto[]
   */
  #[\Override]
  public  function findManyByIdCategoria(int $id_categoria, bool $ativo = true): array {
    try {
      $condicao = $ativo ? " AND p.ativo = true" : '';
      $result = $this->entityManager
        ->createQuery('SELECT p FROM App\Models\Produto p LEFT JOIN p.categoria c 
          WHERE c.id = :id_categoria'.$condicao)
        ->setParameter('id_categoria', $id_categoria)
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id): ?Produto {
    try {
      $produto = $this->entityManager->find(Produto::class, $id);

      return $produto;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findByDescription(string $descricao, int $id = 0): ?Produto {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT p FROM App\Models\Produto p 
        WHERE p.descricao = :descricao
          AND p.id NOT IN (:id)')
        ->setParameters([
          'descricao' => $descricao,
          'id' => $id
        ])
        ->getResult();

      return $result[0] ?? null;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
