<?php

namespace App\Provider\Database\Interface;

use App\Provider\Sql\SQLBuilder;

interface IDatabase extends IDatabaseConnection {
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool;
  function exec(string $sql, $params = []): array|bool;
  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array;
  function query(string $sql, $params = []): array;
  function begin(): ITransaction;
  function transaction(): ITransaction;
}
