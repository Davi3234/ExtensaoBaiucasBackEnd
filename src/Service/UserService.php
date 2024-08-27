<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Provider\Database\Database;
use App\Provider\Database\IDatabase;
use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SQL;
use App\Provider\Zod\Z;

class UserService {
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

  function getOne($args) {
    $id = $args['id'];

    $users = $this->userRepository->findFirstOrThrow([
      'where' => [
        SQL::eq('id', $id)
      ]
    ]);

    return $users;
  }

  function create($args) {
    $dto = Z::object([
        'name' => Z::string(),
        'login' => Z::string(),
      ])
      ->parse($args);

    $insertBuilder = new InsertSQLBuilder;

    $sql = $insertBuilder
      ->insert('"user"')
      ->params('name', 'login')
      ->value([
        'name' => $dto->name,
        'login' => $dto->name,
      ])
      ->returning('*')
      ->toSql();

    $params = $insertBuilder->getParams();

    $result = $this->database->exec($sql, $params);

    return $result;
  }

  function delete($args) {
    $id = $args['id'];

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
