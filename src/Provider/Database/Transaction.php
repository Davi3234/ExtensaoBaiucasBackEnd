<?php

namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Database\Interface\ITransaction;

class Transaction implements ITransaction {
  /**
   * @var IDatabase
   */
  protected $database = null;
  protected $active = false;

  function __construct(IDatabase $database) {
    $this->database = $database;
  }

  static function fromDatabase(IDatabase $connection) {
    return new static($connection);
  }

  #[\Override]
  function begin(): self {
    if ($this->active)
      throw new DatabaseException('Transaction already active');

    $this->database->exec('BEGIN');
    $this->active = true;
    return $this;
  }

  #[\Override]
  function commit(): self {
    if (!$this->active)
      throw new DatabaseException('Transaction not active');

    $this->database->exec('COMMIT');
    $this->active = false;
    return $this;
  }

  #[\Override]
  function rollback(): self {
    if (!$this->active)
      throw new DatabaseException('Transaction not active');

    $this->database->exec('ROLLBACK');
    $this->active = false;
    return $this;
  }

  #[\Override]
  function save(): TransactionCheckpoint {
    return $this->checkpoint()->save();
  }

  #[\Override]
  function checkpoint(): TransactionCheckpoint {
    return TransactionCheckpoint::fromDatabase($this->database);
  }
}
