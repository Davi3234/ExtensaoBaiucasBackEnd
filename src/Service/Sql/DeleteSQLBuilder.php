<?php

namespace App\Service\Sql;

class DeleteSQLBuilder extends SQLConditionBuilder implements ISQLReturningBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules['DELETE'] = '';
    $this->clausules['USING'] = [];
    $this->clausules['RETURNING'] = [];
  }

  /**
   * Method responsible to define DELETE clausule
   * @param string $table Table name
   * @return static
   */
  function delete($table) {
    $this->clausules['DELETE'] = $table;
    return $this;
  }

  /**
   * Method responsible to define USING clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @return static
   */
  function using($table, $alias = '') {
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
    if (!$this->clausules['DELETE'])
      throw new \Exception('Table name not defined for clausule "DELETE"');

    return "DELETE FROM $this->clausules['DELETE']";
  }

  /**
   * Method responsible for generating the sql for clausule USING
   * @return string
   */
  function usingToSql() {
    if (!$this->clausules['USING'])
      return '';

    return 'USING ' . implode(', ', $this->clausules['USING']);
  }

  /**
   * Method responsible for generating the sql for clausule RETURNING
   * @return string
   */
  function returningToSql() {
    if (!$this->clausules["RETURNING"])
      return '';

    return 'RETURNING ' . implode(', ', $this->clausules["RETURNING"]);
  }
}
