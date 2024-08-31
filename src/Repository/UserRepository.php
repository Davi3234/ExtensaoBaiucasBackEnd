<?php

namespace App\Repository;

use App\Common\IRepositoryActions;
use App\Common\Repository;
use App\Model\User;
use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
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
  }

  function delete(DeleteSQLBuilder $deleteBuilder) {
  }

  function checkExistsOrTrow(SelectSQLBuilder $selectBuilder) {
  }

  function isExists(SelectSQLBuilder $selectBuilder) {
  }

  function findFirstOrThrow(SelectSQLBuilder $selectBuilder) {
  }

  function findFirst(SelectSQLBuilder $selectBuilder) {
  }

  function findUniqueOrThrow(SelectSQLBuilder $selectBuilder) {
  }

  function findUnique(SelectSQLBuilder $selectBuilder) {
  }

  function query(SelectSQLBuilder $selectBuilder) {
  }

  function findMany(SelectSQLBuilder $selectBuilder) {
  }

  function count(SelectSQLBuilder $selectBuilder) {
  }
}
