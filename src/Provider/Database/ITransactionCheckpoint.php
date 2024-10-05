<?php

namespace App\Provider\Database;

interface ITransactionCheckpoint {
  function save(): self;
  function release(): self;
  function rollback(): self;
}
