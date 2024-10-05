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

  function begin(): self {
    if ($this->active)
      throw new DatabaseException('Transaction already active');

    $this->database->exec('BEGIN');
    $this->active = true;
    return $this;
  }

  function commit(): self {
    if (!$this->active)
      throw new DatabaseException('Transaction not active');

    $this->database->exec('COMMIT');
    $this->active = false;
    return $this;
  }

  function rollback(): self {
    if (!$this->active)
      throw new DatabaseException('Transaction not active');

    $this->database->exec('ROLLBACK');
    $this->active = false;
    return $this;
  }

  function save(): TransactionCheckpoint {
    return $this->checkpoint()->save();
  }

  function checkpoint(): TransactionCheckpoint {
    return TransactionCheckpoint::fromDatabase($this->database);
  }
}
