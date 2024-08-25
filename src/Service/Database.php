<?php

namespace App\Service;

use App\Exception\InternalServerErrorException;

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

    if ($this->connection === false)
      throw new InternalServerErrorException('Failed to connect to the database');
  }

  function __destruct() {
    $this->close();
  }

  function close() {
    $result = pg_close($this->connection);
    return $result;
  }

  function getConnection() {
    return $this->connection;
  }
}

class Database extends DatabaseConnection implements IDatabase {
  function exec(string $sql, $params = []) {
    $result = $this->sendPgQueryParam($sql, $params);

    if ($result)
      $result = pg_fetch_assoc($result);

    return $result ?: true;
  }

  function query(string $sql, $params = []) {
    $result = $this->sendPgQueryParam($sql, $params);

    $raw = [];
    while ($row = pg_fetch_assoc($result)) {
      $raw[] = $row;
    }

    return $raw;
  }

  private function sendPgQueryParam(string $sql, $params = []) {
    try {
      $result = pg_query_params($this->connection, $sql, $params);

      return $result;
    } catch (\Exception $err) {
      throw new InternalServerErrorException($err->getMessage());
    }
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
