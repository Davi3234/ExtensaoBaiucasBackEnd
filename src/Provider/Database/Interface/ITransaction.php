<?php

namespace App\Provider\Database\Interface;

interface ITransaction {
  function begin(): self;
  function rollback(): self;
  function commit(): self;
  function save(): ITransactionCheckpoint;
  function checkpoint(): ITransactionCheckpoint;
}
