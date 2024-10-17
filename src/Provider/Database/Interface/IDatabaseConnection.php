<?php

namespace App\Provider\Database\Interface;

interface IDatabaseConnection {

  /**
   * Estabelece a conexão com o banco de dados
   * @return void
   */
  function connect();

  /**
   * Fecha a conexão com o banco de dados
   * @return void
   */
  function close();

  /**
   * Retorna uma mensagem de erro da última operação de banco de dados
   * @return string Mensagem de erro da última operação realizada
   */
  function getError(): string;

  /**
   * Retorna o status atual da conexão banco de dados conectado
   * @return bool Status atual da conexão com o banco de dados
   */
  function status(): bool;
}
