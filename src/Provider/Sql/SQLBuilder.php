<?php

namespace App\Provider\Sql;


abstract class SQLBuilder {

  /**
   * @var array<string, array{sqlTemplates: (string|SelectSQLBuilder)[], params: (string|number|boolean|null)[]}[]>
   */
  protected array $clauses = [];

  /**
   * @var array<string, string>
   */
  protected array $clausesOrder = [];

  function buildSqlOnly() {
    $template = $this->getAllTemplates();
    $sql = '';

    foreach ($template['sqlTemplates'] as $key => $sqlTemplates) {
      if ($key > 0) {
        $param = $template['params'][$key - 1];

        if (is_string($param))
          $param = "'$param'";

        if (is_bool($param))
          $param = $param ? 'TRUE' : 'FALSE';

        $sql .= " $param ";
      }
      $sql .= $sqlTemplates;
    }

    return $sql;
  }

  function build() {
    $template = $this->getAllTemplates();
    $sql = '';

    foreach ($template['sqlTemplates'] as $key => $sqlTemplates) {
      if ($key > 0)
        $sql .= " $$key ";
      $sql .= $sqlTemplates;
    }

    return [
      'sql' => trim($sql),
      'params' => $template['params'],
    ];
  }

  function getAllTemplatesWithParentheses() {
    $template = $this->getAllTemplates();

    if (count($template['sqlTemplates'])) {
      $template['sqlTemplates'][0] = "({$template['sqlTemplates'][0]}";
      $lastKey = array_key_last($template['sqlTemplates']);
      $template['sqlTemplates'][$lastKey] = "{$template['sqlTemplates'][$lastKey]})";
    }

    return $template;
  }

  /**
   * @return array{sqlTemplates: string[], params: (string|number|boolean|null)[]}
   */
  function getAllTemplates() {
    $sqlTemplates = [];
    $params = [];

    foreach ($this->clausesOrder as $handler) {
      if (!method_exists($this, $handler))
        continue;

      $templates = $this->$handler();

      $sqlTemplates = self::merge_templates(' ', $sqlTemplates, $templates['sqlTemplates']);
      $params = array_merge($params, $templates['params']);
    }

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  protected static function merge_templates(string $separator, array ...$arrays) {
    $templatesMerged = [];

    foreach ($arrays as $key => $array) {
      if ($key > 0) {
        $current = array_shift($array);

        if ($templatesMerged) {
          $last = array_pop($templatesMerged);
          $templatesMerged[] = trim($last . $separator . $current);
        } else {
          $templatesMerged[] = trim($current);
        }
      }

      $templatesMerged = array_merge($templatesMerged, $array);
    }

    return $templatesMerged;
  }
}
