<?php

namespace App\Common;

use App\Exception\NotFoundException;
use App\Provider\Database\IDatabase;
use App\Provider\Sql\SelectSQLBuilder;

abstract class Repository {
  protected IDatabase $database;

  function __construct(IDatabase $database) {
    $this->database = $database;
  }

  function findFirstOrThrow(SelectSQLBuilder $queryBuilder) {
    $result = $this->findFirst($queryBuilder);

    if (!$result)
      throw new NotFoundException('Register not found');

    return $result;
  }

  function findFirst(SelectSQLBuilder $queryBuilder) {
    $sql = $queryBuilder
      ->limit(1)
      ->toSql();
    $params = $queryBuilder->getParams();

    $result = $this->database->query($sql, $params);

    if (!$result || !isset($result[0]))
      return null;

    return $result[0];
  }

  function findMany(SelectSQLBuilder $queryBuilder) {
    $sql = $queryBuilder
      ->toSql();
    $params = $queryBuilder->getParams();

    $result = $this->database->query($sql, $params);

    return $result;
  }
}
