<?php

namespace App\Common;

use App\Provider\Database\EntityManagerCreator;
use Doctrine\ORM\EntityManager;

/**
 * @template TModel of Model
 */
abstract class Repository {

  protected EntityManager $entityManager;
  
  function __construct() {
    $this->entityManager = EntityManagerCreator::getInstance()->getEntityManager();
  }

  protected function __execSql(string $sql, $params = []) {
    return $this->entityManager;
  }

  protected function __querySql(string $sql, $params = []) {
    return $this->entityManager;
  }

  protected function __exec() {
    return $this->entityManager;
  }

  protected function __findOne() {
    return $this->__findMany()[0];
  }

  protected function __findMany() {
    return $this->entityManager;
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
