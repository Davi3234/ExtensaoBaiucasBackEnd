<?php

namespace App\Migration\Sql;

class DeleteSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules["DELETEFROM"] = [['sqlTemplates' => [], 'params' => []]];

    $this->clausulesOrder = [
      'DELETEFROM' => 'getTemplateDeleteFrom',
      'WHERE' => 'getTemplateWhere',
    ];
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

  function deleteFrom(string $table) {
    $this->clausules["DELETEFROM"][0]['sqlTemplates'] = [$table];

    return $this;
  }

  protected function getTemplateWhere() {
    if (!$this->clausules['WHERE']) {
      throw new SqlBuilderException('There must be at least one exclusion condition in the "WHERE" statement.');
    }

    return parent::getTemplateWhere();
  }
}

$sqlBuilder = SQL::deleteFrom('"user"')
  ->where(
    SQL::eq('id', 1)
  );

$sql = $sqlBuilder->build();

console($sql['sql'], $sql['params']);
