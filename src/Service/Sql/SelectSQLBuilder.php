<?php

namespace App\Service\Sql;

class SelectSQLBuilder extends SQLConditionBuilder {

  /**
   * Method responsible to define SELECT clausule
   * @param string ...$fields Fields to be selected
   * @return static
   */
  function select(...$fields) {
    $this->clausules['SELECT'] = SQL::select(...$fields);

    return $this;
  }

  /**
   * Method responsible to define FROM clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @return static
   */
  function from($table, $alias = '') {
    if (!isset($this->clausules['FROM']))
      $this->clausules['FROM'] = [];

    $this->clausules['FROM'] = SQL::from($table, $alias);

    return $this;
  }

  /**
   * Method responsible to define JOIN clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @param string $on On Condition of the relation
   * @return static
   */
  function join($table, $alias, $on) {
    return $this->createjoin(SQL::join($table, $alias, $on));
  }

  /**
   * Method responsible to define LEFT JOIN clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @param string $on On Condition of the relation
   * @return static
   */
  function leftJoin($table, $alias, $on) {
    return $this->createjoin(SQL::leftJoin($table, $alias, $on));
  }

  /**
   * Method responsible to define RIGHT JOIN clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @param string $on On Condition of the relation
   * @return static
   */
  function rightJoin($table, $alias, $on) {
    return $this->createjoin(SQL::rightJoin($table, $alias, $on));
  }

  /**
   * Method responsible to define INNER JOIN clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @param string $on On Condition of the relation
   * @return static
   */
  function innerJoin($table, $alias, $on) {
    return $this->createjoin(SQL::innerJoin($table, $alias, $on));
  }

  /**
   * Method responsible to define FULL JOIN clausule
   * @param string $table Table name
   * @param string $alias Alias name
   * @param string $on On Condition of the relation
   * @return static
   */
  function fullJoin($table, $alias, $on) {
    return $this->createjoin(SQL::fullJoin($table, $alias, $on));
  }

  /**
   * Prepare data sql to JOIN clausule
   * @param string $sql Sql of the relation
   * @return static
   */
  private function createjoin($sqlClausule) {
    if (!isset($this->clausules['JOIN']))
      $this->clausules['JOIN'] = [];

    $this->clausules['JOIN'][] = $sqlClausule;

    return $this;
  }

  /**
   * Method responsible to define ORDER BY clausule
   * @param string|numeric ...$orderByArgs Order by arguments
   * @return static
   */
  function orderBy(...$orderByArgs) {
    if (!isset($this->clausules['ORDERBY']))
      $this->clausules['ORDERBY'] = [];

    $this->clausules['ORDERBY'] = array_merge($this->clausules['ORDERBY'], $orderByArgs);

    return $this;
  }

  /**
   * Method responsible to define GROUP BY clausule
   * @param string|numeric ...$groupByArgs Group by arguments
   * @return static
   */
  function groupBy(...$groupByArgs) {
    if (!isset($this->clausules['GROUPBY']))
      $this->clausules['GROUPBY'] = [];

    $this->clausules['GROUPBY'] = array_merge($this->clausules['GROUPBY'], $groupByArgs);

    return $this;
  }

  /**
   * Method responsible to define LIMIT clausule
   * @param string|numeric $limit Limit argument
   * @return static
   */
  function limit($limit) {
    $this->clausules['LIMIT'] = $limit;

    return $this;
  }

  /**
   * Method responsible to define OFFSET clausule
   * @param string|numeric $offset Offset argument
   * @return static
   */
  function offset($offset) {
    $this->clausules['OFFSET'] = $offset;

    return $this;
  }

  /**
   * Method responsible to define HAVING clausule
   * @param string|SQLConditionBuilder ...$conditions Condictions arguments
   * @return static
   */
  function having(...$conditions) {
    if (!isset($this->clausules['HAVIING']))
      $this->clausules['HAVIING'] = [];

    $conditions = array_map(function ($condition) {
      if ($condition instanceof SelectSQLBuilder)
        return $condition->toSql();

      return $condition;
    }, $conditions);

    $this->clausules['HAVIING'] = array_merge($this->clausules['HAVIING'], $conditions);

    return $this;
  }

  /**
   * Method responsible for generating the sql
   * @return string
   */
  function toSql() {
    $sqlStatement = [
      $this->withToSql(),
      $this->selectFieldsToSql(),
      $this->fromToSql(),
      $this->joinToSql(),
      $this->whereToSql(),
      $this->groupByToSql(),
      $this->havingToSql(),
      $this->orderByToSql(),
      $this->limitToSql(),
      $this->offsetToSql(),
    ];

    $sqlStatement = array_filter($sqlStatement, function ($statement) {
      return !!$statement;
    });

    return implode(' ', $sqlStatement);
  }

  /**
   * Method responsible for generating the sql for clausule SELECT
   * @return string
   */
  function selectFieldsToSql() {
    if (!isset($this->clausules['SELECT']))
      return 'SELECT *';

    return $this->clausules['SELECT']['sql'];
  }

  /**
   * Method responsible for generating the sql for clausule FROM
   * @return string
   */
  function fromToSql() {
    if (!isset($this->clausules['FROM']) || $this->clausules['FROM']['sql'] == '')
      throw new \Exception('Clausule "FROM" not defined');

    return $this->clausules['FROM']['sql'];
  }

  /**
   * Method responsible for generating the sql for clausule JOIN
   * @return string
   */
  function joinToSql() {
    if (!isset($this->clausules['JOIN']) || count($this->clausules['JOIN']) == 0)
      return '';

    $sql = array_map(function ($clausuleJoin) {
      return $clausuleJoin['sql'];
    }, $this->clausules['JOIN']);

    return implode(' ', $sql);
  }

  /**
   * Method responsible for generating the sql for clausule GROUP BY
   * @return string
   */
  function groupByToSql() {
    if (!isset($this->clausules['GROUPBY']) || count($this->clausules['GROUPBY']) == 0)
      return '';

    return 'GROUP BY ' . implode(', ', $this->clausules['GROUPBY']);
  }

  /**
   * Method responsible for generating the sql for clausule HAVING
   * @return string
   */
  function havingToSql() {
    if (!isset($this->clausules['HAVING']))
      return '';

    $conditions = array_map(function ($condition) {
      return $this->buildCondition($condition);
    }, $this->clausules['HAVING']);

    array_unshift($conditions, '1 = 1');

    return 'HAVING ' . implode(' AND ', $conditions);
  }

  /**
   * Method responsible for generating the sql for clausule ORDER BY
   * @return string
   */
  function orderByToSql() {
    if (!isset($this->clausules['ORDERBY']) || count($this->clausules['ORDERBY']) == 0)
      return '';

    return 'ORDER BY ' . implode(', ', $this->clausules['ORDERBY']);
  }

  /**
   * Method responsible for generating the sql for clausule LIMIT
   * @return string
   */
  function limitToSql() {
    if (!isset($this->clausules['LIMIT']) || $this->clausules['LIMIT']['sql'] == '')
      return '';

    return $this->clausules['LIMIT']['sql'];
  }

  /**
   * Method responsible for generating the sql for clausule OFFSET
   * @return string
   */
  function offsetToSql() {
    if (!isset($this->clausules['OFFSET']) || $this->clausules['OFFSET']['sql'] == '')
      return '';

    return $this->clausules['OFFSET']['sql'];
  }
}
