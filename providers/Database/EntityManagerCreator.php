<?php

namespace Provider\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Provider\Database\Enums\Driver;

class EntityManagerCreator {
  protected static $instance;
  private $entityManager;

  private function __construct() {
    $config = ORMSetup::createAttributeMetadataConfiguration(
      paths: [__DIR__ . '/../../App/Models'],
      isDevMode: true,
    );

    $connection = $this->getConnection($config);

    $this->entityManager = new EntityManager($connection, $config);

    $this->initializeDatabase();
  }

  private function getConnection($config){

    if(env('DB_DRIVER') == Driver::SQLITE->value){
      return DriverManager::getConnection([
        'driver' => env('DB_DRIVER'),
        'path' => __DIR__ . '/../../database.sqlite',
        'memory' => true,
      ], $config);
    }

    return DriverManager::getConnection([
      'driver' => env('DB_DRIVER'),
      'dbname' => env('DB_DATABASE'),
      'host' => env('DB_HOST'),
      'port' => env('DB_PORT'),
      'user' => env('DB_USERNAME'),
      'password' => env('DB_PASSWORD'),
    ], $config);
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

  private function initializeDatabase(){
    if(env('DB_ENV') == 'test'){
      $this->resetDatabase();
    }
  }

  private function resetDatabase(){

    $schemaTool = new SchemaTool($this->entityManager);

    $entities = $this->entityManager->getMetadataFactory()->getAllMetadata();

    $schemaTool->dropSchema($entities);

    $schemaTool->createSchema($entities);
  }
}
