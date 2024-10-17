<?php

namespace App\Provider\Sql\Builder;

use App\Provider\Sql\SQL;
use App\Provider\Sql\SqlBuilderException;

class UpdateSQLBuilder extends ReturningConditionSQLBuilder {

  function __construct() {
    parent::__construct();

    $this->clauses["UPDATE"] = [['sqlTemplates' => [], 'params' => []]];
    $this->clauses["SET"] = [];

    $this->clausesOrder = [
      'UPDATE' => 'getTemplateUpdate',
      'SET' => 'getTemplateSet',
    ];
  }

  function update(string $table) {
    $this->clauses["UPDATE"][0]['sqlTemplates'] = [$table];

    return $this;
  }

  /**
   * @param array<string, string|int|float|bool>[] $values
   */
  function values(array $values) {
    $paramsName = $this->clauses["SET"][0]['sqlTemplates'];
    $paramsSql = $this->clauses["SET"][0]['params'];

    foreach ($values as $param => $value) {
      $paramsName[$param] = $param;
      $paramsSql[$param] = $value;
    }

    $this->clauses["SET"][0] = [
      'sqlTemplates' => $paramsName,
      'params' => $paramsSql
    ];

    return $this;
  }

  protected function getTemplateUpdate() {
    $sqlParams = $this->clauses["UPDATE"][0]['sqlTemplates'];

    if (!$sqlParams || !$sqlParams[0]) {
      throw new SqlBuilderException('Table name not defined for clause "UPDATE"');
    }

    return [
      'sqlTemplates' => ["UPDATE $sqlParams[0]"],
      'params' => [],
    ];
  }

  protected function getTemplateSet() {
    $sqlValues = $this->clauses["SET"][0];

    if (!$sqlValues) {
      throw new SqlBuilderException('Values not defined for clause "UPDATE"');
    }

    $sqlTemplates = [];
    $params = [];

    foreach ($sqlValues['sqlTemplates'] as $param) {
      $sqlTemplates = self::merge_templates(', ', $sqlTemplates, ["$param = ", '']);
      $params[] = $sqlValues['params'][$param];
    }

    $sqlTemplates[0] = "SET $sqlTemplates[0]";

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  #[\Override]
  protected function getTemplateWhere() {
    if (!$this->clauses['WHERE']) {
      throw new SqlBuilderException('There must be at least one update condition in the "WHERE" statement.');
    }

    return parent::getTemplateWhere();
  }
}

$sqlBuilder = SQL::update('"user"')
  ->values(['name' => 'Dan'])
  ->values(['login' => 'dan@gmail.com'])
  ->returning('*');
