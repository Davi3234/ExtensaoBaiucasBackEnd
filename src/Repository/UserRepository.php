<?php

namespace App\Repository;

use App\Common\Repository;
use App\Model\User;
use App\Provider\Sql\SQL;

class UserRepository extends Repository {

  /**
   * @return User
   */
  function create(User $user) {
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
  function update(User $user) {
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
   * @return User[]
   */
  function findMany() {
    return parent::_queryModel(
      SQL::select()->from('"user"'),
      User::class
    );
  }

  /**
   * @return User
   */
  function findById(int $id) {
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
