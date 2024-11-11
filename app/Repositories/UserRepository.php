<?php

namespace App\Repositories;

use Common\Repository;
use App\Models\User;
use Doctrine\ORM\Cache\Exception\FeatureNotImplemented;
use Provider\Database\DatabaseException;

class UserRepository extends Repository implements IUserRepository {

  #[\Override]
  public function create(User $user): User {
    try {
      $this->entityManager->persist($user);
      $this->entityManager->flush();

      return $user;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function update(User $user): User {
    try {
      throw new FeatureNotImplemented('Method "update" from "UserRepository" not implemented');
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function deleteById(int $id) {
    try {
      $user = $this->findById($id);

      $this->entityManager->remove($user);
      $this->entityManager->flush();
      
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  /**
   * @return User[]
   */
  #[\Override]
  public function findMany(): array {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT u FROM App\Models\User u')
        ->getResult();

      return $result;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findById(int $id): ?User {
    try {
      $user = $this->entityManager->find(User::class, $id);

      return $user;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }

  #[\Override]
  public function findByLogin(string $login): ?User {
    try {
      $result = $this->entityManager
        ->createQuery('SELECT u FROM App\Models\User u WHERE u.login = :login')
        ->setParameters([
          'login' => $login,
        ])
        ->getResult();

      return $result[0] ?? null;
    } catch (\Exception $e) {
      throw new DatabaseException($e->getMessage());
    }
  }
}
