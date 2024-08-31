<?php

namespace App\Provider\Database;

use App\Provider\Sql\SQLBuilder;

interface IDatabaseConnection {
  function connect();
  function close();
  function getError(): string;
}

interface IDatabase extends IDatabaseConnection {
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool;
  function exec(string $sql, $params = []): array|bool;
  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array;
  function query(string $sql, $params = []): array|bool;
}

interface ITransaction {
  function begin();
  function rollback();
  function commit();
}

interface ITransactionCheckpoint {
  function save();
  function release();
  function rollback();
}
