<?php

namespace App\Provider\Sql;

class InsertSQLBuilder extends SQLBuilder implements ISQLReturningBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules['INSERT'] = '';
    $this->clausules['PARAMS'] = [];
    $this->clausules['VALUES'] = [];
    $this->clausules['RETURNING'] = [];
  }

  /**
   * Method responsible to define INSERT clausule
   * @param string $table Table name
   * @return static
   */
  function insert($table) {
    $this->clausules['INSERT'] = $table;
    return $this;
  }

  /**
   * Method responsible to define PARAMS clausule
   * @param string ...$params Parameters to insert
   * @return static
   */
  function params(...$params) {
    $this->clausules["PARAMS"] = $params;
    return $this;
  }

  /**
   * Method responsible to define VALUES clausule
   * @param array<string, string|numeric|boolean> ...$fields Values of the parameters
   * @return static
   */
  function value(...$values) {
    foreach ($values as $key => $valueParams) {
      foreach ($valueParams as $param => $value)
        $values[$key][$param] = $this->createParam($value);
    }

    $this->clausules["VALUES"] = array_merge($this->clausules["VALUES"], $values);
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
      $this->insertToSql(),
      $this->paramsToSql(),
      $this->valuesToSql(),
      $this->returningToSql(),
    ];

    $sqlStatement = array_filter($sqlStatement, function ($statement) {
      return !!$statement;
    });

    return implode(' ', $sqlStatement);
  }

  /**
   * Method responsible for generating the sql for clausule INSERT
   * @return string
   */
  function insertToSql() {
    if (!$this->clausules['INSERT'])
      throw new \Exception('Table name not defined for clausule "INSERT"');

    return "INSERT INTO $this->clausules['INSERT']";
  }

  /**
   * Method responsible for generating the sql for clausule PARAMS
   * @return string
   */
  function paramsToSql() {
    return "(" . implode(', ', $this->clausules['PARAMS']) . ")";
  }

  /**
   * Method responsible for generating the sql for clausule VALUES
   * @return string
   */
  function valuesToSql() {
    $values = $this->getValuesParams();

    $values = array_map(function ($value) {
      return "(" . implode(', ', $value) . ")";
    }, $values);

    return 'VALUES ' . implode(', ', $values);
  }

  /**
   * Return the list of the values to respective param
   * @return array<array<string>>
   */
  private function getValuesParams() {
    $valuesList = [];

    foreach ($this->clausules["VALUES"] as $values) {
      $valueInsert = [];

      foreach ($this->clausules["PARAMS"] as $param) {
        if (!isset($values[$param]) || $values[$param] == '')
          throw new \Exception("Value to param \"$param\" not defined to clausule \"VALUES\"");

        $valueInsert[$param] = $values[$param];
      }

      array_push($valuesList, $valueInsert);
    }

    return $valuesList;
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
