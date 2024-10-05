<?php

namespace App\Common;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\UpdateSQLBuilder;

/**
 * @template TModel of Model
 */
abstract class Repository {

  function __construct(
    protected IDatabase $database
  ) {
  }

  protected function __execSql(string $sql, $params = []): array|bool {
    return $this->database->exec($sql, $params);
  }

  protected function __querySql(string $sql, $params = []): array {
    return $this->database->query($sql, $params);
  }

  protected function __exec(InsertSQLBuilder|UpdateSQLBuilder|DeleteSQLBuilder $sqlBuilder): array {
    $sqlBuilder->returning('*');

    return $this->database->execFromSqlBuilder($sqlBuilder);
  }

  protected function __findOne(SelectSQLBuilder $selectBuilder): ?array {
    return $this->__findMany($selectBuilder)[0];
  }

  protected function __findMany(SelectSQLBuilder $selectBuilder): array {
    return $this->database->queryFromSqlBuilder($selectBuilder);
  }

  /**
   * @param class-string<TModel> $modelConstructor
   * @return TModel[]
   */
  protected static function toModelList(array $rawList, string $modelConstructor): array {
    return array_map(function ($raw) use ($modelConstructor) {
      return self::toModel($raw, $modelConstructor);
    }, $rawList);
  }

  /**
   * @param class-string<TModel> $modelConstructor
   * @return ?TModel
   */
  protected static function toModel(array|null $raw, string $modelConstructor): object {
    return $raw ? $modelConstructor::__loadModel($raw) : null;
  }
}
