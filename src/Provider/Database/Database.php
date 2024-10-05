<?php

namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Sql\Builder\SQLBuilder;
use App\Exception\Exception;

class Database extends DatabaseConnection implements IDatabase {

  #[\Override]
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool {
    $sql = $sqlBuilder->build();

    $result = $this->exec($sql['sql'], $sql['params']);

    return $result;
  }

  #[\Override]
  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array {
    $sql = $sqlBuilder->build();

    return $this->query($sql['sql'], $sql['params']);
  }

  #[\Override]
  function exec(string $sql, $params = []): array|bool {
    $result = $this->sendPgQueryParam($sql, $params);

    return $result ?: true;
  }

  #[\Override]
  function query(string $sql, $params = []): array {
    $result = $this->sendPgQueryParam($sql, $params);

    return $result ?: [];
  }

  private function sendPgQueryParam($sql, $params = []): array {
    try {
      $result = @pg_query_params($this->connection, $sql, $params);

      if ($result === false)
        throw new DatabaseException($this->getError());

      $raw = [];
      while ($row = pg_fetch_assoc($result)) {
        $raw[] = $row;
      }

      return $raw;
    } catch (Exception $err) {
      throw $err;
    } catch (\Exception $err) {
      throw new DatabaseException($err->getMessage());
    }
  }

  #[\Override]
  function begin(): Transaction {
    return $this->transaction()->begin();
  }

  #[\Override]
  function transaction(): Transaction {
    return Transaction::fromDatabase($this);
  }
}
