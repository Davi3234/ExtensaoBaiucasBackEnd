<?php

namespace App\Controller;

use App\Core\Components\Request;
use App\Service\IDatabase;
use App\Service\Database;
use App\Service\Sql\DeleteSQLBuilder;
use App\Service\Sql\InsertSQLBuilder;
use App\Service\Sql\SelectSQLBuilder;
use App\Service\Sql\SQL;

class UserController {
  private IDatabase $database;

  function __construct() {
    $this->database = Database::newConnection();
  }

  function query() {
    $queryBuilder = new SelectSQLBuilder;

    $result = $this->database->query($queryBuilder->from('"user"')->toSql());

    return $result;
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
