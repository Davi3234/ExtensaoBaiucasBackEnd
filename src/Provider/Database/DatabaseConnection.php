<?php

namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabaseConnection;
use PgSql\Connection;

class DatabaseConnection implements IDatabaseConnection {

  private static ?Connection $globalConnection = null;
  protected ?Connection $connection;

  function __construct(?Connection $connection = null) {
    $this->connection = $connection;
  }

  static function getGlobalConnection(): static {
    if (static::$globalConnection == null) {
      static::$globalConnection = static::newConnection()->getConnection();
    }

    return static::fromConnection(static::$globalConnection);
  }

  static function newConnection(): static {
    $database = new static();
    $database->connect();

    return $database;
  }

  static function fromDatabaseConnection(DatabaseConnection $connection) {
    return static::fromConnection($connection->getConnection());
  }

  static function fromConnection(Connection $connection) {
    return new static($connection);
  }

  #[\Override]
  function connect() {
    if ($this->connection != null) {
      throw new DatabaseException('Connection link database already connected');
    }

    $this->connection = @pg_connect(env('DATABASE_URL'));

    if ($this->connection === false)
      throw new DatabaseException('Failed to connect to the database');
  }

  #[\Override]
  function close() {
    pg_close($this->connection);
  }

  function getConnection() {
    return $this->connection;
  }

  #[\Override]
  function getError(): string {
    return pg_last_error($this->connection);
  }
}
