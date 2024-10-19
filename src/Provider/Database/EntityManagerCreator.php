<?php

namespace App\Provider\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class EntityManagerCreator {
  protected static $instance;
  private $entityManager;

  private function __construct() {
    $config = ORMSetup::createAttributeMetadataConfiguration(
      paths: [__DIR__ . '/../../Model'],
      isDevMode: true,
    );

    $connection = DriverManager::getConnection([
      'driver' => getenv('DB_DRIVER'),
      'dbname' => getenv('DB_DATABASE'),
      'host' => getenv('DB_HOST'),
      'port' => getenv('DB_PORT'),
      'user' => getenv('DB_USERNAME'),
      'password' => getenv('DB_PASSWORD'),
    ], $config);

    $this->entityManager = new EntityManager($connection, $config);
  }

  public static function getInstance(): self {
    if (self::$instance === null) {
      self::$instance = new EntityManagerCreator();
    }
    return self::$instance;
  }

  public function getEntityManager(): EntityManager {
    return $this->entityManager;
  }
}
