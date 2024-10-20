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
}
