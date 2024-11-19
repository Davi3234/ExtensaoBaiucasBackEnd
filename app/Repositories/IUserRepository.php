<?php

namespace App\Repositories;

use App\Models\User;

interface IUserRepository {

  function create(User $user): User;
  function update(User $user): User;
  function deleteById(int $id);

  /**
   * @param array{pageIndex: ?int, limit: ?int} $args
   * @return User[]
   */
  function findMany(array $args = []): array;
  function count(): int;
  function findByLogin(string $login): ?User;
  function findById(int $id): ?User;
}
