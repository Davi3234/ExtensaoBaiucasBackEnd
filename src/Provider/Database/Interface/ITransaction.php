<?php

namespace App\Provider\Database\Interface;

interface ITransaction {

  /**
   * Inicia uma nova transação
   * @return self Retorna a própria instância da transação
   */
  function begin(): self;

  /**
   * Reverte todas as operações realizadas desde o início da transação
   * @return self Retorna a própria instância da transação
   */
  function rollback(): self;

  /**
   * Confirma todas as operações realizadas durante a transação
   * @return self Retorna a própria instância da transação
   */
  function commit(): self;

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
}
