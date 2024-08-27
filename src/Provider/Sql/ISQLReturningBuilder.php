<?php

namespace App\Provider\Sql;

interface ISQLReturningBuilder {

  /**
   * Method responsible to define RETURNING clausule
   * @param string ...$fields Fields to be returned
   * @return static
   */
  function returning(...$fields);

  /**
   * Method responsible for generating the sql for clausule RETURNING
   * @return string
   */
  function returningToSql();
}
