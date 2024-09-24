<?php

namespace App\Repository;

use App\Common\Repository;
use App\Model\User;
use App\Provider\Sql\SQL;

class UserRepository extends Repository {

  /**
   * @return User
   */
  function create(User $user): User {
    /**
     * @var User
     */
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

  /**
   * @return User
   */
  function update(User $user): User {
    /**
     * @var User
     */
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

  /**
   * @return User
   */
  function delete(User $user): User {
    /**
     * @var User
     */
    $userDeleted = parent::_delete(
      SQL::deleteFrom('"user"')
        ->where(
          SQL::eq('id', $user->getId())
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

  /**
   * @return User
   */
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
}
