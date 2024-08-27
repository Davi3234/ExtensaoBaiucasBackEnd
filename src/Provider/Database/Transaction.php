<?php

namespace App\Provider\Database;

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

  function begin() {
    if ($this->active)
      throw new \Exception('Transaction already active');

    $this->database->exec('BEGIN');
    $this->active = true;
  }

  function commit() {
    if (!$this->active)
      throw new \Exception('Transaction not active');

    $this->database->exec('COMMIT');
    $this->active = false;
  }

  function rollback() {
    if (!$this->active)
      throw new \Exception('Transaction not active');

    $this->database->exec('ROLLBACK');
    $this->active = false;
  }

  function save() {
    $checkpoint = TransactionCheckpoint::fromDatabase($this->database);
    $checkpoint->save();

    return $checkpoint;
  }
}

class TransactionCheckpoint implements ITransactionCheckpoint {
  /**
   * @var IDatabase
   */
  protected $database = null;
  protected $active = false;
  private $name;

  function __construct(IDatabase $database) {
    $this->database = $database;
    $this->name = uuid();
  }

  function save() {
    if ($this->active)
      throw new \Exception('Checkpoint transaction already active');

    $this->database->exec("SAVEPOINT \"$this->name\"");
    $this->active = true;
  }

  function release() {
    if (!$this->active)
      throw new \Exception('Checkpoint transaction not active');

    $this->database->exec("RELEASE SAVEPOINT \"$this->name\"");
    $this->active = false;
  }

  function rollback() {
    if (!$this->active)
      throw new \Exception('Checkpoint transaction not active');

    $this->database->exec("ROLLBACK TO SAVEPOINT \"$this->name\"");
    $this->active = false;
  }

  static function fromDatabase(IDatabase $connection) {
    return new static($connection);
  }
}
