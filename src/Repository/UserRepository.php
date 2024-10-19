<?php

namespace App\Repository;

use App\Common\Repository;
use App\Model\User;

/**
 * @extends parent<User>
 */
class UserRepository extends Repository implements IUserRepository {

  #[\Override]
  public function create(User $user): User {
    return new User();

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
