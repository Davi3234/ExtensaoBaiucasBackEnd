<?php

namespace App\Repository;

use App\Common\Repository;
use App\Model\User;
use App\Provider\Sql\SQL;

class UserRepository extends Repository implements IUserRepository {

  function create(User $user): User {
    /** @var User */
    $userCreated = parent::_create(
      SQL::insertInto('"user"')
        ->params('name', 'login')
        ->values([
          'name' => $user->getName(),
          'login' => $user->getLogin(),
        ])
    );

    return $userCreated;
  }

  function update(User $user): User {
    /** @var User */
    $userUpdated = parent::_update(
      SQL::update('"user"')
        ->values([
          'name' => $user->getName(),
          'login' => $user->getLogin(),
        ])
        ->where(
          SQL::eq('id', $user->getId())
        )
    );

    return $userUpdated;
  }

  function deleteById(int $id): User {
    /** @var User */
    $userDeleted = parent::_delete(
      SQL::deleteFrom('"user"')
        ->where(
          SQL::eq('id', $id)
        )
    );

    return $userDeleted;
  }

  /**
   * @return User[]
   */
  function findMany(): array {
    return parent::_queryModel(
      SQL::select('us.*')->from('"user"', 'us'),
      User::class
    );
  }

  function findById(int $id): ?User {
    return parent::_queryOneModel(
      SQL::select()
        ->from('"user"')
        ->where(
          SQL::eq('id', $id)
        )
        ->limit(1),
      User::class
    );
  }

  function findByLogin(string $login): ?User {
    return parent::_queryOneModel(
      SQL::select()
        ->from('"user"')
        ->where(
          SQL::eq('login', $login)
        )
        ->limit(1),
      User::class
    );
  }
}
