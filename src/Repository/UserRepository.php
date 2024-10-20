<?php

namespace App\Repository;

use App\Common\Repository;
use App\Model\User;
use App\Exception\Http\InternalServerErrorException;
use Exception;

/**
 * @extends parent<User>
 */
class UserRepository extends Repository implements IUserRepository {

  #[\Override]
  public function create(User $user): User {
    try {
      $this->entityManager->persist($user);
      $this->entityManager->flush();

      return $user;

    } catch (Exception $e) {
      throw new InternalServerErrorException($e->getMessage());
    }

  }

  #[\Override]
  public function update(User $user): User {
    return new User();

  }

  #[\Override]
  public function deleteById(int $id): User {
    return new User();
  }

  /**
   * @return User[]
   */
  #[\Override]
  public function findMany(): array {
    return [];
  }

  #[\Override]
  public function findById(int $id): ?User {
    return new User();
  }

  #[\Override]
  public function findByLogin(string $login): ?User {
    return new User();
  }
}
