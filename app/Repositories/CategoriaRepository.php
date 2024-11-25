<?php

namespace App\Repositories;

use Common\Repository;
use App\Models\Categoria;
use Doctrine\ORM\Cache\Exception\FeatureNotImplemented;
use Provider\Database\DatabaseException;

class CategoriaRepository extends Repository implements ICategoriaRepository {

  #[\Override]
  public function create(Categoria $categoria): Categoria {
    try {
      $this->entityManager->persist($categoria);
      $this->entityManager->flush();

      return $categoria;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function update(Categoria $categoria): Categoria {
    try {
      $this->entityManager->persist($categoria);
      $this->entityManager->flush();

      return $categoria;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id) {
    try {
      $categoria = $this->findById($id);

      $this->entityManager->remove($categoria);
      $this->entityManager->flush();
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return Categoria[]
   */

  #[\Override]
  public function findMany(): array {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT c FROM App\Models\Categoria c')
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id): ?Categoria {
    try {
      $categoria = $this->entityManager->find(Categoria::class, $id);

      return $categoria;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findByDescription(string $descricao): ?Categoria {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT c FROM App\Models\Categoria c WHERE c.descricao = :descricao')
        ->setParameters([
          'descricao' => $descricao,
        ])
        ->getResult();

      return $result[0] ?? null;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
