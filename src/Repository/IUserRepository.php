<?php

namespace App\Repository;

use App\Model\User;

interface IUserRepository {

  function create(User $user): User;
  function update(User $user): User;
  function delete(User $user): User;

  /**
   * @return User[]
   */
  function findMany(): array;
  function findById(int $id): ?User;
}
