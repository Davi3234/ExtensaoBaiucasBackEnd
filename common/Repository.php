<?php

namespace Common;

use Doctrine\ORM\EntityManager;
use Provider\Database\EntityManagerCreator;

abstract class Repository {

  protected EntityManager $entityManager;

  function __construct() {
    $this->entityManager = EntityManagerCreator::getInstance()->getEntityManager();
  }
}
