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
    $result = parent::_create(
      SQL::insertInto('"user"')
        ->params('name', 'login')
        ->values([
          'name' => $user->getName(),
          'login' => $user->getLogin(),
        ])
    );

    return self::toModel($result, User::class);
  }

  function update(User $user): User {
    $result = parent::_update(
      SQL::update('"user"')
        ->values([
          'name' => $user->getName(),
          'login' => $user->getLogin(),
        ])
        ->where(
          SQL::eq('id', $user->getId())
        )
    );

    return self::toModel($result, User::class);
  }

  function deleteById(int $id): User {
    $result = parent::_delete(
      SQL::deleteFrom('"user"')
        ->where(
          SQL::eq('id', $id)
        )
    );

    return self::toModel($result, User::class);
  }

  /**
   * @return User[]
   */
  function findMany(): array {
    $result = parent::_query(
      SQL::select('us.*')->from('"user"', 'us')
    );

    return self::toModelList($result, User::class);
  }

  function findById(int $id): ?User {
    $result = parent::_queryOne(
      SQL::select()
        ->from('"user"')
        ->where(
          SQL::eq('id', $id)
        )
        ->limit(1)
    );

    return self::toModel($result, User::class);
  }

  function findByLogin(string $login): ?User {
    $result = parent::_queryOne(
      SQL::select()
        ->from('"user"')
        ->where(
          SQL::eq('login', $login)
        )
        ->limit(1)
    );

    return self::toModel($result, User::class);
  }
}
