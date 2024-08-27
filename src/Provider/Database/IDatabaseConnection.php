<?php

namespace App\Provider\Database;

interface IDatabaseConnection {
  function connect();
  function close();
  function getError(): string;
}

interface IDatabase extends IDatabaseConnection {
  function exec(string $sql, $params = []): array|bool;
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
