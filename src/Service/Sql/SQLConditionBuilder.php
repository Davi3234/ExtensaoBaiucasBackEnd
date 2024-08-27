<?php

namespace App\Service\Sql;

class SQLConditionBuilder extends SQLBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules['WITH'] = '';
    $this->clausules['WHERE'] = [];
  }

  /**
   * Method responsible to define WITH clausule
   * @param string|SQLConditionBuilder $sqlClausule Sql reference of the condiction
   * @return static
   */
  function with($sqlClausule) {
    if ($sqlClausule instanceof SQLConditionBuilder)
      $sql = $sqlClausule->toSql();
    else
      $sql = $sqlClausule;

    $this->clausules['WITH'] = $sql;

    return $this;
  }

  /**
   * Method responsible to define the condictions to query
   * @param array{sql: string, clausule: string}[] ...$conditions
   * @return static
   */
  function where(...$conditions) {
    $this->clausules['WHERE'] = array_merge($this->clausules['WHERE'], $conditions);

    return $this;
  }

  function whereToSql() {
    if (!$this->clausules['WHERE'])
      return 'WHERE 1 = 1';

    $conditions = array_map(function ($condition) {
      return $this->buildCondition($condition);
    }, $this->clausules['WHERE']);

    array_unshift($conditions, '1 = 1');

    return 'WHERE ' . implode(' AND ', $conditions);
  }

  function withToSql() {
    if (!$this->clausules['WITH'])
      return '';

    $sql = array_map(function ($clausuleWith) {
      return $clausuleWith['sql'];
    }, $this->clausules['WITH']);

    return 'WITH ' . implode(', ', $sql);
  }

  protected function buildCondition($condition) {
    if (isset($condition['nested'])) {
      $nestedConditions = array_map(function ($cond) {
        return $this->buildCondition($cond);
      }, $condition['nested']);

      if ($condition['type'] == 'NOT')
        return 'NOT (' . implode(' AND ', $nestedConditions) . ')';

      return '(' . implode(' ' . $condition['type'] . ' ', $nestedConditions) . ')';
    }

    return $condition['sql'];
  }
}
