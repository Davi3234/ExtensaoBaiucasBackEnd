<?php

namespace App\Service\Database;

interface IDatabaseConnection {
  function connect();
  function close();
  function getError();
}

interface IDatabase extends IDatabaseConnection {
  function exec(string $sql, $params = []);
  function query(string $sql, $params = []);
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
