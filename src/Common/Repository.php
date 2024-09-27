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

  protected function _create(InsertSQLBuilder $insertBuilder): array {
    $insertBuilder->returning('*');

    return $this->database->execFromSqlBuilder($insertBuilder);
  }

  protected function _update(UpdateSQLBuilder $updateBuilder): array {
    $updateBuilder->returning('*');

    return $this->database->execFromSqlBuilder($updateBuilder);
  }

  protected function _delete(DeleteSQLBuilder $deleteBuilder): array {
    $deleteBuilder->returning('*');

    return $this->database->execFromSqlBuilder($deleteBuilder);
  }

  protected function _queryOneModel(SelectSQLBuilder $selectBuilder, string $modelConstructor): ?object {
    return $this->_queryModel($selectBuilder, $modelConstructor)[0];
  }

  /**
   * @return object[]
   */
  protected function _queryModel(SelectSQLBuilder $selectBuilder, string $modelConstructor): array {
    $result = $this->_query($selectBuilder);

    $dataModel = [];
    foreach ($result as $raw) {
      $dataModel[] = $modelConstructor::_loadModel($raw);
    }

    return $dataModel;
  }

  protected function _queryOne(SelectSQLBuilder $selectBuilder): ?array {
    return $this->_query($selectBuilder)[0];
  }

  protected function _query(SelectSQLBuilder $selectBuilder): array {
    return $this->database->queryFromSqlBuilder($selectBuilder);
  }
}
