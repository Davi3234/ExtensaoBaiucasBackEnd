<?php

namespace Provider\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class EntityManagerCreator {
  protected static $instance;
  private $entityManager;

  private function __construct() {
    $config = ORMSetup::createAttributeMetadataConfiguration(
      paths: [__DIR__ . '/../../App/Models'],
      isDevMode: true,
    );

    $connection = DriverManager::getConnection([
      'driver' => env('DB_DRIVER'),
      'dbname' => env('DB_DATABASE'),
      'host' => env('DB_HOST'),
      'port' => env('DB_PORT'),
      'user' => env('DB_USERNAME'),
      'password' => env('DB_PASSWORD'),
    ], $config);

    $this->entityManager = new EntityManager($connection, $config);
  }

  static function getInstance(): self {
    if (self::$instance === null) {
      self::$instance = new EntityManagerCreator();
    }
    return self::$instance;
  }

  function getEntityManager(): EntityManager {
    return $this->entityManager;
  }
}
