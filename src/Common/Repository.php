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

  protected function _create(InsertSQLBuilder $insertBuilder) {
    $insertBuilder->returning('*');

    return $this->database->execFromSqlBuilder($insertBuilder);
  }

  protected function _update(UpdateSQLBuilder $updateBuilder) {
    $updateBuilder->returning('*');

    return $this->database->execFromSqlBuilder($updateBuilder);
  }

  protected function _delete(DeleteSQLBuilder $deleteBuilder) {
    $deleteBuilder->returning('*');

    return $this->database->execFromSqlBuilder($deleteBuilder);
  }

  protected function _query(SelectSQLBuilder $selectBuilder) {
    return $this->database->queryFromSqlBuilder($selectBuilder);
  }

  protected function _queryModel(SelectSQLBuilder $selectBuilder, $modelConstructor) {
    $result = $this->database->queryFromSqlBuilder($selectBuilder);

    $dataModel = [];
    foreach ($result as $raw) {
      $dataModel[] = $modelConstructor::_loadModel($raw);
    }

    return $dataModel;
  }
}
