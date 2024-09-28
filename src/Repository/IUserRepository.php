<?php

namespace App\Repository;

use App\Model\User;

interface IUserRepository {

  function create(User $user): User;
  function update(User $user): User;
  function deleteById(int $id): User;

  /**
   * @return User[]
   */
  function findMany(): array;
  function findByLogin(string $login): ?User;
  function findById(int $id): ?User;
}
