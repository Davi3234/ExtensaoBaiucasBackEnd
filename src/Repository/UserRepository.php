<?php

namespace App\Repository;

use App\Common\Repository;
use App\Model\User;

/**
 * @extends parent<User>
 */
class UserRepository extends Repository implements IUserRepository {

  #[\Override]
  function create(User $user): User {
    return new User();

  }

  #[\Override]
  function update(User $user): User {
    return new User();

  }

  #[\Override]
  function deleteById(int $id): User {
    return new User();
  }

  /**
   * @return User[]
   */
  #[\Override]
  function findMany(): array {
    return [];
  }

  #[\Override]
  function findById(int $id): ?User {
    return new User();
  }

  #[\Override]
  function findByLogin(string $login): ?User {
    return new User();
  }
}
