<?php

namespace App\Repository;

use App\Common\Repository;
use App\Exception\NotFoundException;
use App\Model\User;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\SQL;
use App\Provider\Sql\UpdateSQLBuilder;

class UserRepository extends Repository {
  function create(User ...$users) {
    $data = array_map(function ($user) {
      return [
        'login' => $user->getLogin(),
        'name' => $user->getName(),
      ];
    }, $users);

    $insertBuilder = SQL::insert('"user"')
      ->params('login', 'name')
      ->value(...$data);

    $result = parent::_create($insertBuilder);

    return $result;
  }

  function update(UpdateSQLBuilder $updateBuilder) {
    $updateBuilder->update('"user');

    $result = parent::_update($updateBuilder);

    return $result;
  }

  /**
   * @return array{where: array}
   */
  function delete(array $args) {
    $deleteBuilder = SQL::delete('"user"')
      ->where(...$args);

    $result = parent::_delete($deleteBuilder);

    return $result;
  }

  function checkExistsOrTrow(SelectSQLBuilder $selectBuilder) {
    $result = $this->isExists($selectBuilder);

    if (!$result)
      throw new NotFoundException('User not found');
  }

  function isExists(SelectSQLBuilder $selectBuilder) {
    $result = $this->findFirst($selectBuilder);

    return !!$result;
  }

  function findFirstOrThrow(SelectSQLBuilder $selectBuilder) {
    $result = $this->findFirst($selectBuilder);

    if (!$result)
      throw new NotFoundException('User not found');

    return $result;
  }

  function findFirst(SelectSQLBuilder $selectBuilder): array|null {
    $selectBuilder
      ->from('"user')
      ->limit(1);

    $result = parent::_query($selectBuilder);

    if (!isset($result[0]))
      return null;

    return $result[0];
  }

  function findMany(SelectSQLBuilder $selectBuilder) {
    $selectBuilder->from('"user"');

    $result = parent::_queryModel($selectBuilder, User::class);

    return $result;
  }

  function count(SelectSQLBuilder $selectBuilder) {
    $selectBuilder
      ->select('COUNT(*)')
      ->from('"user');

    $result = parent::_query($selectBuilder);

    return $result;
  }
}
