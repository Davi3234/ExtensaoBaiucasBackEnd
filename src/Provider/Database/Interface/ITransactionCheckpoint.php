<?php

namespace App\Provider\Database\Interface;

interface ITransactionCheckpoint {

  /**
   * Salva o estado atual da transação no checkpoint
   * @return self Retorna a própria instância do checkpoint
   */
  function save(): self;

  /**
   * Libera o ponto de salvamento, confirmando as operações realizadas até o save do checkpoint
   * @return self Retorna a própria instância do checkpoint
   */
  function release(): self;

  /**
   * Reverte as operações até o ponto de salvamento
   * @return self Retorna a própria instância do checkpoint
   */
  function rollback(): self;
}
