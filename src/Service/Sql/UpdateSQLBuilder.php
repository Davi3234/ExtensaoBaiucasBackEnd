<?php

namespace App\Service\Sql;

class UpdateSQLBuilder extends SQLConditionBuilder implements ISQLReturningBuilder {
  
  function __construct() {
    parent::__construct();

    $this->clausules['UPDATE'] = '';
    $this->clausules['SET'] = [];
    $this->clausules['RETURNING'] = [];
  }

  /**
   * Method responsible to define UPDATE clausule
   * @param string $table Table name
   * @return static
   */
  function update($table) {
    $this->clausules['UPDATE'] = $table;
    return $this;
  }

  /**
   * Method responsible to define UPDATE clausule
   * @param array<string, mixed> $raw Values to set clausule
   * @return static
   */
  function setValue($raw) {
    if (in_array('', array_keys($raw)))
      throw new \Exception("Param name not defined to clausule \"SET\"");

    foreach($raw as $param => $value) {
      $raw[$param] = $this->createParam($value);
    }

    $this->clausules["SET"] = array_merge($this->clausules["SET"], $raw);
    return $this;
  }

  /**
   * Method responsible to define RETURNING clausule
   * @param string ...$fields Fields to be returned
   * @return static
   */
  function returning(...$fields) {
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
    if (!$this->clausules['UPDATE'])
      throw new \Exception('Table name not defined for clausule "UPDATE"');

    return "UPDATE $this->clausules['UPDATE']";
  }

  /**
   * Method responsible for generating the sql for clausule SET
   * @return string
   */
  function setValueToSql() {
    if (!$this->clausules['SET'])
      return '';

    $setValues = array_map(function ($param, $value) {
      if (!isset($value) || !$value)
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
    if (!$this->clausules["RETURNING"])
      return '';

    return 'RETURNING ' . implode(', ', $this->clausules["RETURNING"]);
  }
}
