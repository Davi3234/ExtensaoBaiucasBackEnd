<?php

namespace App\Service;

class DatabaseConnection implements IDatabaseConnection {
  /**
   * @var \PgSql\Connection
   */
  protected $connection = null;

  function __construct(?\PgSql\Connection $connection = null) {
    $this->connection = $connection;
  }

  static function newConnection() {
    $database = new static();
    $database->connect();

    return $database;
  }

  static function fromDatabaseConnection(DatabaseConnection $connection) {
    return static::fromConnection($connection->getConnection());
  }

  static function fromConnection(\PgSql\Connection $connection) {
    return new static($connection);
  }

  function connect() {
    $this->connection = pg_connect(get_env('DATABASE_URL'));

    return $this->connection !== false;
  }

  function close() {
    return pg_close($this->connection);
  }

  function getConnection() {
    return $this->connection;
  }
}

class Database extends DatabaseConnection implements IDatabase {
  function exec(string $sql, $params = []) {
    return pg_query_params($this->connection, $sql, $params);
  }

  function query(string $sql, $params = []) {
    return pg_query_params($this->connection, $sql, $params);
  }

  function transaction() {
    return Transaction::fromDatabase($this);
  }
}

class Transaction implements ITransaction {
  /**
   * @var IDatabase
   */
  protected $database = null;

  function __construct(IDatabase $database) {
    $this->database = $database;
  }

  static function fromDatabase(IDatabase $connection) {
    return new static($connection);
  }

  function begin() {
    $this->database->exec('BEGIN');
  }

  function commit() {
    $this->database->exec('COMMIT');
  }

  function rollback() {
    $this->database->exec('ROLLBACK');
  }
}
