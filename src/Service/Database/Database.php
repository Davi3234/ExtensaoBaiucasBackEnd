<?php

namespace App\Service\Database;

use App\Exception\InternalServerErrorException;

class DatabaseConnection implements IDatabaseConnection {
  /**
   * @var \PgSql\Connection
   */
  protected $connection = null;

  function __construct(?\PgSql\Connection $connection = null) {
    $this->connection = $connection;
  }

  /**
   * @return static
   */
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

  function close() {
    $result = pg_close($this->connection);
    return $result;
  }

  function getConnection() {
    return $this->connection;
  }

  function getError() {
    return pg_last_error($this->connection);
  }
}

class Database extends DatabaseConnection implements IDatabase {
  function exec(string $sql, $params = []) {
    $result = $this->sendPgQueryParam($sql, $params);

    if ($result !== true)
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
      $result = @pg_query_params($this->connection, $sql, $params);

      if ($result === false)
        throw new InternalServerErrorException($this->getError());

      return $result;
    } catch (\Exception $err) {
      throw new InternalServerErrorException($err->getMessage());
    }
  }

  function transaction() {
    return Transaction::fromDatabase($this);
  }
}
