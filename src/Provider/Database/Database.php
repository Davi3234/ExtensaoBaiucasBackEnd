<?php

namespace App\Provider\Database;

use App\Exception\Exception;
use App\Provider\Sql\SQLBuilder;

class Database extends DatabaseConnection implements IDatabase {
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool {
    $sql = $sqlBuilder->build();

    $result = $this->exec($sql['sql'], $sql['params']);

    return $result;
  }

  function exec(string $sql, $params = []): array|bool {
    $result = $this->sendPgQueryParam($sql, $params);

    return $result ?: true;
  }

  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array {
    $sql = $sqlBuilder->build();

    return $this->query($sql['sql'], $sql['params']);
  }

  function query(string $sql, $params = []): array {
    $result = $this->sendPgQueryParam($sql, $params);

    return $result ?: [];
  }

  private function sendPgQueryParam($sql, $params = []) {
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

  function begin(): Transaction {
    return $this->transaction()->begin();
  }

  function transaction(): Transaction {
    return Transaction::fromDatabase($this);
  }
}
