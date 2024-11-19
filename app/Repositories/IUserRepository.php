<?php

namespace App\Repositories;

use App\Models\User;

interface IUserRepository {

  function create(User $user): User;
  function update(User $user): User;
  function deleteById(int $id);

  /**
   * @return User[]
   */
  function findMany(): array;
  function findByLogin(string $login): ?User;
  function findById(int $id): ?User;
}
