<?php

namespace App\Service;

interface IDatabaseConnection {
  function connect();
  function close();
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

interface ITransactionCheckpoint extends ITransaction {
}
