<?php

namespace App\Repository;

use App\Common\Repository;
use App\Exception\NotFoundException;
use App\Provider\Sql\SelectSQLBuilder;

class UserRepository extends Repository {

  /**
   * @param array{where: array} $args
   */
  function findFirstOrThrow($args = []) {
    $result = $this->findFirst($args);

    if (!$result)
      throw new NotFoundException('User not found');

    return $result;
  }

  /**
   * @param array{where: array} $args
   */
  function findFirst($args = []) {
    $queryBuilder = new SelectSQLBuilder;

    $sql = $queryBuilder
      ->from('"user"')
      ->where(...$args['where'] ?: [])
      ->limit(1)
      ->toSql();

    $result = $this->database->query($sql);

    if (!$result || !isset($result[0]))
      return null;

    return $result[0];
  }

  /**
   * @param array{where: array} $args
   */
  function findMany($args = []) {
    $queryBuilder = new SelectSQLBuilder;

    $sql = $queryBuilder
      ->from('"user"')
      ->where(...$args['where'] ?: [])
      ->toSql();

    $result = $this->database->query($sql);

    return $result;
  }
}
