<?php

namespace App\Provider\Sql;

class DeleteSQLBuilder extends ReturningConditionSQLBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules["DELETEFROM"] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules["USING"] = [];

    $this->clausulesOrder = [
      'DELETEFROM' => 'getTemplateDeleteFrom',
      'USING' => 'getTemplateUsing',
      'WHERE' => 'getTemplateWhere',
    ];
  }

  function deleteFrom(string $table) {
    $this->clausules["DELETEFROM"][0]['sqlTemplates'] = [$table];

    return $this;
  }

  function using(SelectSQLBuilder|string $table, string $alias) {
    $this->clausules["USING"][] = [
      'sqlTemplates' => [$table, $alias],
      'params' => []
    ];

    return $this;
  }

  protected function getTemplateDeleteFrom() {
    $sqlParams = $this->clausules["DELETEFROM"][0]['sqlTemplates'];

    if (!$sqlParams || !$sqlParams[0]) {
      throw new SqlBuilderException('Table name not defined for clause "DELETE"');
    }

    return [
      'sqlTemplates' => ["DELETE FROM $sqlParams[0]"],
      'params' => [],
    ];
  }

  protected function getTemplateUsing() {
    $sqlUsings = $this->clausules['USING'];

    if (!$sqlUsings)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    $sqlTemplates = [];
    $params = [];

    foreach ($sqlUsings as $key => $sqlJoin) {
      [$joinTable, $alias] = $sqlJoin['sqlTemplates'];

      if ($joinTable instanceof SelectSQLBuilder) {
        $joinTable = $joinTable->getAllTemplatesWithParentheses();

        $params = array_merge($params, $joinTable['params']);

        $joinTable = $joinTable['sqlTemplates'];
      } else {
        $joinTable = [$joinTable];
      }

      if ($key > 0) {
        $sqlTemplates[array_key_last($sqlTemplates)] .= ',';
      }

      $sqlTemplates = self::merge_templates(' ', $sqlTemplates, $joinTable, ["AS $alias"]);
    }

    $sqlTemplates[0] = "USING $sqlTemplates[0]";

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  protected function getTemplateWhere() {
    if (!$this->clausules['WHERE']) {
      throw new SqlBuilderException('There must be at least one exclusion condition in the "WHERE" statement.');
    }

    return parent::getTemplateWhere();
  }
}

$sqlBuilder = SQL::deleteFrom('"user"')
  ->using('perfil', 'pr')
  ->using(SQL::select()->from('perfil'), 'pr1')
  ->where(
    SQL::eq('id', 1)
  )
  ->returning('*');
