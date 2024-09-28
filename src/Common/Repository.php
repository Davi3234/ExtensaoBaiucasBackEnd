<?php

namespace App\Common;

use App\Provider\Database\IDatabase;
use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\UpdateSQLBuilder;

/**
 * @template TModel
 */
abstract class Repository {

  function __construct(
    protected IDatabase $database
  ) {
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

  protected function _queryOne(SelectSQLBuilder $selectBuilder): ?array {
    return $this->_query($selectBuilder)[0];
  }

  protected function _query(SelectSQLBuilder $selectBuilder): array {
    return $this->database->queryFromSqlBuilder($selectBuilder);
  }

  /**
   * @return TModel[]
   */
  protected static function toModelList(array $rawList, string $modelConstructor): array {
    return array_map(function($raw) use ($modelConstructor) {
      return self::toModel($raw, $modelConstructor);
    }, $rawList);
  }

  /**
   * @return TModel
   */
  protected static function toModel(array $raw, string $modelConstructor): object {
    return $modelConstructor::_loadModel($raw);
  }
}
