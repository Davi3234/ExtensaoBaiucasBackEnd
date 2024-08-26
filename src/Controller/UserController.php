<?php

namespace App\Controller;

use App\Core\Components\Request;
use App\Repository\UserRepository;
use App\Service\Database\Database;
use App\Service\Database\IDatabase;
use App\Service\Sql\DeleteSQLBuilder;
use App\Service\Sql\InsertSQLBuilder;
use App\Service\Sql\SQL;

class UserController {
  private IDatabase $database;
  private UserRepository $userRepository;

  function __construct() {
    $this->database = Database::newConnection();
    $this->userRepository = new UserRepository($this->database);
  }

  function query() {
    $users = $this->userRepository->findMany();

    return $users;
  }

  function getOne(Request $request) {
    $id = $request->getParam('id');

    $users = $this->userRepository->findFirstOrThrow([
      'where' => [
        SQL::eq('id', $id)
      ]
    ]);

    return $users;
  }

  function create(Request $request) {
    $name = $request->getBody('name');
    $login = $request->getBody('login');

    $insertBuilder = new InsertSQLBuilder;

    $sql = $insertBuilder
      ->insert('"user"')
      ->params('name', 'login')
      ->value([
        'name' => $name,
        'login' => $login,
      ])
      ->returning('*')
      ->toSql();

    $params = $insertBuilder->getParams();

    $result = $this->database->exec($sql, $params);

    return $result;
  }

  function delete(Request $request) {
    $id = $request->getParam('id');

    $deleteBuilder = new DeleteSQLBuilder;

    $sql = $deleteBuilder
      ->delete('"user"')
      ->where(
        SQL::eq('id', $id)
      )
      ->toSql();
    $params = $deleteBuilder->getParams();

    $result = $this->database->exec($sql, $params);

    return $result;
  }
}
