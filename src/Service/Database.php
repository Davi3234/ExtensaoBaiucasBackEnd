<?php

namespace App\Service;

interface IDatabase {
  function conect();
  function close();
  function exec($sql, $params = []);
  function query($sql, $params = []);
}

class Database implements IDatabase {
  /**
   * @var \PgSql\Connection
   */
  private $connection = null;

  function conect() {
    $this->connection = pg_connect("host= port= dbname= user= password=") !== false;

    return $this->connection !== false;
  }

  function close() {
    return pg_close($this->connection);
  }

  function exec($sql, $params = []) {
    $key = time().'-'.mt_rand();

    pg_send_prepare($this->connection, $key, $sql);

    if (!pg_send_execute($sql, $key, $params)) {
      return pg_last_error($this->connection);
    }
    
    return pg_get_result($this->connection);
  }

  function query($sql, $params = []) {
    $result = pg_send_execute($this->connection, $sql, $params);

    if ($result === false)

    return pg_get_result($this->connection);
  }
}