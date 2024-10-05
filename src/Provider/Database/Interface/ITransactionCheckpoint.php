<?php

namespace App\Provider\Database\Interface;

interface ITransactionCheckpoint {
  function save(): self;
  function release(): self;
  function rollback(): self;
}
