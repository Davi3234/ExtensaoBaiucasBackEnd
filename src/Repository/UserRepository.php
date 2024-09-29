<?php

namespace App\Repository;

use App\Provider\Sql\SQL;
use App\Common\Repository;
use App\Model\User;

/**
 * @extends parent<User>
 */
class UserRepository extends Repository implements IUserRepository {

  function create(User $user): User {
    $rowCreated = parent::__exec(
      SQL::insertInto('users')
        ->params('name', 'login')
        ->values([
          'name' => $user->getName(),
          'login' => $user->getLogin(),
        ])
    );

    return self::toModel($$rowCreated, User::class);
  }

  function update(User $user): User {
    $rowUpdated = parent::__exec(
      SQL::update('users')
        ->values([
          'name' => $user->getName(),
          'login' => $user->getLogin(),
        ])
        ->where(
          SQL::eq('id', $user->getId())
        )
    );

    return self::toModel($rowUpdated, User::class);
  }

  function deleteById(int $id): User {
    $rowDeleted = parent::__exec(
      SQL::deleteFrom('users')
        ->where(
          SQL::eq('id', $id)
        )
    );

    return self::toModel($rowDeleted, User::class);
  }

  /**
   * @return User[]
   */
  function findMany(): array {
    $rows = parent::__findMany(
      SQL::select('us.*')->from('users', 'us')
    );

    return self::toModelList($rows, User::class);
  }

  function findById(int $id): ?User {
    $row = parent::__findOne(
      SQL::select()
        ->from('users')
        ->where(
          SQL::eq('id', $id)
        )
        ->limit(1)
    );

    return self::toModel($row, User::class);
  }

  function findByLogin(string $login): ?User {
    $row = parent::__findOne(
      SQL::select()
        ->from('users')
        ->where(
          SQL::eq('login', $login)
        )
        ->limit(1)
    );

    return self::toModel($row, User::class);
  }
}