<?php

namespace App\Provider\Database;

use App\Exception\Exception;
use App\Exception\InternalServerErrorException;
use App\Provider\Sql\SQLBuilder;

class DatabaseConnection implements IDatabaseConnection {

  /**
   * @var DatabaseConnection
   */
  private static $globalConnection = null;

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
  static function getGlobalConnection() {
    if (static::$globalConnection == null) {
      static::$globalConnection = static::newConnection();
    }

    return static::$globalConnection;
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
    if ($this->connection != null) {
      throw new DatabaseException('Connection link database already connected');
    }

    $this->connection = @pg_connect(get_env('DATABASE_URL'));

    if ($this->connection === false)
      throw new InternalServerErrorException('Failed to connect to the database');
  }

  function close() {
    pg_close($this->connection);
  }

  function getConnection() {
    return $this->connection;
  }

  function getError(): string {
    return pg_last_error($this->connection);
  }
}

class Database extends DatabaseConnection implements IDatabase {
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool {
    $sql = $sqlBuilder->build();

    $result = $this->exec($sql['sql'], $sql['params']);

    return $result;
  }

  function exec(string $sql, $params = []): array|bool {
    $result = $this->sendPgQueryParam($sql, $params);

    return $result ?: true;
  }

  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array {
    $sql = $sqlBuilder->build();

    $result = $this->query($sql['sql'], $sql['params']);

    return $result;
  }

  function query(string $sql, $params = []): array|bool {
    $result = $this->sendPgQueryParam($sql, $params);

    return $result ?: [];
  }

  private function sendPgQueryParam($sql, $params = []) {
    try {
      $result = @pg_query_params($this->connection, $sql, $params);

      if ($result === false)
        throw new DatabaseException($this->getError());

      $raw = [];
      while ($row = pg_fetch_assoc($result)) {
        $raw[] = $row;
      }

      return $raw;
    } catch (Exception $err) {
      throw $err;
    } catch (\Exception $err) {
      throw new InternalServerErrorException($err->getMessage());
    }
  }

  function begin() {
    return $this->transaction()->begin();
  }

  function transaction() {
    return Transaction::fromDatabase($this);
  }
}
