<?php

namespace App\Service\Sql;

class UpdateSQLBuilder extends SQLConditionBuilder implements ISQLReturningBuilder {

  /**
   * Method responsible to define UPDATE clausule
   * @param string $table Table name
   * @return self
   */
  function update($table) {
    $this->clausules['UPDATE'] = SQL::update($table);
    return $this;
  }

  /**
   * Method responsible to define UPDATE clausule
   * @param array<string, mixed> $raw Values to set clausule
   * @return self
   */
  function setValue($raw) {
    if (in_array('', array_keys($raw)))
      throw new \Exception("Param name not defined to clausule \"SET\"");

    if (!isset($this->clausules["SET"]))
      $this->clausules["SET"] = [];

    $this->clausules["SET"] = array_merge($this->clausules["SET"], $raw);
    return $this;
  }

  /**
   * Method responsible to define RETURNING clausule
   * @param string ...$fields Fields to be returned
   * @return self
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
      $this->updateToSql(),
      $this->setValueToSql(),
      $this->whereToSql(),
      $this->returningToSql(),
    ];

    $sqlStatement = array_filter($sqlStatement, function ($statement) {
      return !!$statement;
    });

    return implode(' ', $sqlStatement);
  }

  /**
   * Method responsible for generating the sql for clausule UPDATE
   * @return string
   */
  function updateToSql() {
    if (!isset($this->clausules['UPDATE']) || $this->clausules['UPDATE']['sql'] == '')
      throw new \Exception('Table name not defined for clausule "UPDATE"');

    return $this->clausules['UPDATE']['sql'];
  }

  /**
   * Method responsible for generating the sql for clausule SET
   * @return string
   */
  function setValueToSql() {
    if (!isset($this->clausules['SET']) || count($this->clausules['SET']) == 0)
      return '';

    $setValues = array_map(function ($param, $value) {
      if (!isset($value) || $value == '')
        throw new \Exception("Value to param \"$param\" not defined to clausule \"SET\"");

      return "$param = $value";
    }, array_keys($this->clausules['SET']), array_values($this->clausules['SET']));

    return 'SET ' . implode(', ', $setValues);
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
