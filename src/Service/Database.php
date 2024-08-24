<?php

namespace App\Service;

interface IDatabase {
  function connect();
  function close();
  function exec(string $sql, $params = []);
  function query(string $sql, $params = []);
}

class Database implements IDatabase {
  /**
   * @var \PgSql\Connection
   */
  private $connection = null;

  function connect() {
    $this->connection = pg_connect(get_env('DATABASE_URL'));

    return $this->connection !== false;
  }

  function close() {
    return pg_close($this->connection);
  }

  function exec(string $sql, $params = []) {
    $result = pg_query_params($this->connection, $sql, $params);

    return $result;
  }

  function query(string $sql, $params = []) {
    $result = pg_send_execute($this->connection, $sql, $params);

    if ($result === false)

      return pg_get_result($this->connection);
  }
}
