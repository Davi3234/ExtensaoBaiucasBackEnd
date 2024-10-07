<?php

namespace App\Provider\Database\Interface;

interface ITransaction {

  /**
   * Inicia uma nova transação
   * @return static Retorna a própria instância da transação
   */
  function begin(): static;

  /**
   * Reverte todas as operações realizadas desde o início da transação
   * @return static Retorna a própria instância da transação
   */
  function rollback(): static;

  /**
   * Confirma todas as operações realizadas durante a transação
   * @return static Retorna a própria instância da transação
   */
  function commit(): static;

  /**
   * Cria uma instância de checkpoint da transação da conexão atual com o banco sem iniciar o save do checkpoint (SAVE)
   * @return ITransactionCheckpoint Instância de um checkpoint na transação
   */
  function checkpoint(): ITransactionCheckpoint;

  /**
   * Cria uma instância de checkpoint da transação da conexão atual com o banco com o save do checkpoint já iniciado (SAVE)
   * @return ITransactionCheckpoint Instância de um checkpoint na transação
   */
  function save(): ITransactionCheckpoint;

  /**
   * Retorna um boolean indicando se a transação está ativa ou não
   * @return bool Status da transação
   */
  function isActive(): bool;
}
