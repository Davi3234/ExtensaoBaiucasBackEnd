<?php

namespace App\Common;

use App\Provider\Database\IDatabase;
use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\UpdateSQLBuilder;

abstract class Repository {
  protected IDatabase $database;

  function __construct(IDatabase $database) {
    $this->database = $database;
  }

  function create(InsertSQLBuilder $insertBuilder) {
    $insertBuilder->returning('*');

    return $this->database->execFromSqlBuilder($insertBuilder);
  }

  function update(UpdateSQLBuilder $updateBuilder) {
    $updateBuilder->returning('*');

    return $this->database->execFromSqlBuilder($updateBuilder);
  }

  function delete(DeleteSQLBuilder $deleteBuilder) {
    $deleteBuilder->returning('*');

    return $this->database->execFromSqlBuilder($deleteBuilder);
  }

  function query(SelectSQLBuilder $selectBuilder) {
    return $this->database->queryFromSqlBuilder($selectBuilder);
  }
}
