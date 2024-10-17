<?php

namespace App\Provider\Database\Interface;

interface ITransactionCheckpoint {

  /**
   * Salva o estado atual da transação no checkpoint
   * @return static Retorna a própria instância do checkpoint
   */
  function save(): static;

  /**
   * Libera o ponto de salvamento, confirmando as operações realizadas até o save do checkpoint
   * @return static Retorna a própria instância do checkpoint
   */
  function release(): static;

  /**
   * Reverte as operações até o ponto de salvamento
   * @return static Retorna a própria instância do checkpoint
   */
  function rollback(): static;
}
