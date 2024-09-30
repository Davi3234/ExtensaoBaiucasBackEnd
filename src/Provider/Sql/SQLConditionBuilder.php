<?php

namespace App\Provider\Sql;

class SQLConditionBuilder extends SQLBuilder {

  function __construct() {
    $this->clauses['WITH'] = [];
    $this->clauses['WHERE'] = [];
  }

  function with(string $alias, SelectSQLBuilder $selectBuilder) {
    $this->clauses['WITH'][] = [
      'sqlTemplates' => [$alias, $selectBuilder],
      'params' => [],
    ];

    return $this;
  }

  /**
   * @param array{sqlTemplates: string[], params: string|number|boolean|null[]}[] $conditions
   */
  function where(array $conditions) {
    $this->clauses['WHERE'] = array_merge($this->clauses['WHERE'], $conditions);

    return $this;
  }

  protected function getTemplateWith() {
    $sqlTemplatesWith = $this->clauses['WITH'];

    if (!$sqlTemplatesWith)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    $sqlTemplates = ['WITH '];
    $params = [];

    foreach ($sqlTemplatesWith as $key => $sqlTemplate) {
      [$alias, $selectBuilder] = $sqlTemplate['sqlTemplates'];

      if (true) {
        $selectSqlTemplate = $selectBuilder->getAllTemplatesWithParentheses();

        if ($key > 0) {
          $sqlTemplates[array_key_last($sqlTemplates)] .= ', ';
        }

        $sqlTemplates[array_key_last($sqlTemplates)] .= "$alias AS";

        $sqlTemplates = self::merge_templates(' ', $sqlTemplates, $selectSqlTemplate['sqlTemplates']);
        $params = array_merge($params, $selectSqlTemplate['params']);
      }
    }

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  protected function getTemplateWhere() {
    $sqlTemplates = ['1 = 1'];
    $params = [];

    foreach ($this->clauses['WHERE'] as $template) {
      $templates = $this->buildCondition($template);

      $sqlTemplates = $this->merge_templates(' AND ', $sqlTemplates, $templates['sqlTemplates']);
      $params = array_merge($params, $templates['params']);
    }

    return [
      'sqlTemplates' => $this->merge_templates(' ', ['WHERE'], $sqlTemplates),
      'params' => $params,
    ];
  }

  protected function buildCondition(array $condition) {
    if (isset($condition['nested'])) {
      $nestedConditions = array_map(function ($cond) {
        return $this->buildCondition($cond);
      }, $condition['nested']);

      $sqlTemplates = [];
      $params = [];

      foreach ($nestedConditions as $nestedCondition) {
        if (!$sqlTemplates)
          $sqlTemplates = $nestedCondition['sqlTemplates'];
        else {
          $type = $condition['type'] != 'NOT' ? $condition['type'] : 'AND';
          $sqlTemplates = $this->merge_templates("$type ", $sqlTemplates, $nestedCondition['sqlTemplates']);
        }

        $params = array_merge($params, $nestedCondition['params']);
      }

      if ($sqlTemplates) {
        $sqlTemplates[0] = "($sqlTemplates[0]";
        $sqlTemplates[array_key_last($sqlTemplates)] = "{$sqlTemplates[array_key_last($sqlTemplates)]})";

        if ($condition['type'] == 'NOT') {
          $sqlTemplates[0] = "NOT $sqlTemplates[0]";
        }
      }

      $condition['sqlTemplates'] = $sqlTemplates;
      $condition['params'] = $params;
    }

    return $condition;
  }
}
