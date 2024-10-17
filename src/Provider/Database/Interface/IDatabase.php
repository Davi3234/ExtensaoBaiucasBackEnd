<?php

namespace App\Provider\Database\Interface;

use App\Provider\Sql\Builder\SQLBuilder;

interface IDatabase extends IDatabaseConnection {

  /**
   * Executa uma instrução SQL direta.
   *
   * Executa a consulta SQL fornecida como string e, opcionalmente, recebe parâmetros para substituição. 
   * Retorna o resultado como array ou `false` em caso de erro.
   *
   * @param string $sql String contendo a instrução SQL a ser executada.
   * @param array $params Parâmetros opcionais para a execução do SQL.
   * @return array|bool Retornará `true` caso tenha dado sucesso ou um array com o resultado obtido
   */
  function exec(string $sql, $params = []): array|bool;

  /**
   * Executa uma consulta SELECT direta.
   *
   * Executa a consulta SELECT fornecida como string, utilizando parâmetros opcionais para substituição,
   * e retorna os resultados como um array.
   *
   * @param string $sql String contendo a consulta SELECT
   * @param array $params Parâmetros opcionais para a consulta
   * @return array Resultado da consulta como um array de dados
   */
  function query(string $sql, $params = []): array;

  /**
   * Executa um comando SQL a partir de um SQLBuilder.
   *
   * Este método utiliza uma instância de SQLBuilder para gerar uma consulta SQL. 
   * Ele executa a consulta e retorna o resultado como array, ou `false` em caso de erro.
   *
   * @param SQLBuilder $sqlBuilder Instância de SQLBuilder que cria a consulta SQL
   * @return array|bool Retornará `true` caso tenha dado sucesso ou um array com o resultado obtido
   */
  function execFromSqlBuilder(SQLBuilder $sqlBuilder): array|bool;

  /**
   * Executa uma consulta SELECT a partir de um SQLBuilder.
   *
   * Este método utiliza um SQLBuilder para construir e executar uma consulta do tipo SELECT,
   * retornando os resultados como um array.
   *
   * @param SQLBuilder $sqlBuilder Instância de SQLBuilder que gera a consulta SELECT
   * @return array Resultado da consulta como um array de dados
   */
  function queryFromSqlBuilder(SQLBuilder $sqlBuilder): array;

  /**
   * Cria uma instância da transação da conexão atual com o banco sem iniciar o bloco de transação (BEGIN)
   * @return ITransaction Instância de transação
   */
  function transaction(): ITransaction;

  /**
   * Cria uma instância da transação da conexão atual com o banco com o bloco de transação já iniciado (BEGIN)
   * @return ITransaction Instância de transação
   */
  function begin(): ITransaction;
}
