<?php

namespace App\Migration\Sql;


abstract class SQLBuilder {

  /**
   * @var array<string,array{sqlTemplates: (string|SelectSQLBuilder)[], params: (string|number|boolean|null)[]}[]>
   */
  protected array $clausules = [];

  /**
   * @var array<string, string>
   */
  protected array $clausulesOrder = [];

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
      'sql' => $sql,
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

    foreach ($this->clausulesOrder as $clausule => $handler) {
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
        $anterior = array_pop($templatesMerged);
        $primeiroAtual = array_shift($array);
        $templatesMerged[] = trim($anterior . $separator . $primeiroAtual);
      }

      $templatesMerged = array_merge($templatesMerged, $array);
    }

    return $templatesMerged;
  }
}
