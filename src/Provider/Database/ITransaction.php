<?php

namespace App\Provider\Database;

interface ITransaction {
  function begin(): self;
  function rollback(): self;
  function commit(): self;
  function save(): ITransactionCheckpoint;
  function checkpoint(): ITransactionCheckpoint;
}
