<?php

namespace App\Provider\Database;

class TransactionCheckpoint implements ITransactionCheckpoint {
  /**
   * @var IDatabase
   */
  protected $database = null;
  protected $active = false;
  private string $name;

  function __construct(IDatabase $database) {
    $this->database = $database;
    $this->name = uuid();
  }

  function save(): self {
    if ($this->active)
      throw new DatabaseException('Checkpoint transaction already active');

    $this->database->exec("SAVEPOINT \"$this->name\"");
    $this->active = true;
    return $this;
  }

  function release(): self {
    if (!$this->active)
      throw new DatabaseException('Checkpoint transaction not active');

    $this->database->exec("RELEASE SAVEPOINT \"$this->name\"");
    $this->active = false;
    return $this;
  }

  function rollback(): self {
    if (!$this->active)
      throw new DatabaseException('Checkpoint transaction not active');

    $this->database->exec("ROLLBACK TO SAVEPOINT \"$this->name\"");
    $this->active = false;
    return $this;
  }

  static function fromDatabase(IDatabase $connection) {
    return new static($connection);
  }
}
