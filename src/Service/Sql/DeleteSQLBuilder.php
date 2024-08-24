<?php

namespace App\Service\Sql;

class DeleteSQLBuilder extends SQLConditionBuilder implements ISQLReturningBuilder {

  /**
   * Method responsible to define DELETE clausule
   * @param string $table Table name
   * @return static
   */
  function delete($table) {
    $this->clausules['DELETE'] = SQL::delete($table);
    return $this;
  }

  /**
   * Method responsible to define USING clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @return static
   */
  function using($table, $alias = '') {
    if (!isset($this->clausules['USING']))
      $this->clausules['USING'] = [];

    $this->clausules['USING'][] = trim("$table $alias");
    return $this;
  }

  /**
   * Method responsible to define RETURNING clausule
   * @param string ...$fields Fields to be returned
   * @return static
   */
  function returning(...$fields) {
    if (!isset($this->clausules["RETURNING"]))
      $this->clausules["RETURNING"] = [];

    $this->clausules["RETURNING"] = array_merge($this->clausules["RETURNING"], $fields);
    return $this;
  }

  /**
   * Method responsible for generating the sql
   * @return string
   */
  function toSql() {
    $sqlStatement = [
      $this->deleteToSql(),
      $this->usingToSql(),
      $this->whereToSql(),
    ];

    $sqlStatement = array_filter($sqlStatement, function ($statement) {
      return !!$statement;
    });

    return implode(' ', $sqlStatement);
  }

  /**
   * Method responsible for generating the sql for clausule DELETE
   * @return string
   */
  function deleteToSql() {
    if (!isset($this->clausules['DELETE']) || $this->clausules['DELETE']['sql'] == '')
      throw new \Exception('Table name not defined for clausule "DELETE"');

    return $this->clausules['DELETE']['sql'];
  }

  /**
   * Method responsible for generating the sql for clausule USING
   * @return string
   */
  function usingToSql() {
    if (!isset($this->clausules['USING']))
      return '';

    return 'USING ' . implode(', ', $this->clausules['USING']);
  }

  /**
   * Method responsible for generating the sql for clausule RETURNING
   * @return string
   */
  function returningToSql() {
    if (!isset($this->clausules["RETURNING"]))
      return '';

    return 'RETURNING ' . implode(', ', $this->clausules["RETURNING"]);
  }
}
