<?php

namespace App\Provider\Database;

use App\Provider\Database\Interface\IDatabase;
use App\Provider\Sql\Builder\SQLBuilder;
use App\Exception\Exception;

/**
 * Implementação de operações de banco de dados baseadas em SQL, utilizando a conexão PostgreSQL.
 * Extende a classe DatabaseConnection e implementa a interface IDatabase.
 */
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
    $result = $this->sendOperation($sql, $params);

    return $result ?: true;
  }

  #[\Override]
  function query(string $sql, $params = []): array {
    $result = $this->sendOperation($sql, $params);

    return $result ?: [];
  }

  /**
   * Envia uma operação SQL para o PostgreSQL utilizando parâmetros.
   * @param string $sql Instrução SQL a ser executada.
   * @param array $params Parâmetros a serem substituídos na consulta.
   * @return array Resultado da operação como array.
   */
  private function sendOperation(string $sql, array $params = []): array {
    try {
      $result = @pg_query_params($this->connection, $sql, $params);

      if ($result === false) {
        throw new DatabaseException($this->getError());
      }

      return pg_fetch_all($result, PGSQL_ASSOC);
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
