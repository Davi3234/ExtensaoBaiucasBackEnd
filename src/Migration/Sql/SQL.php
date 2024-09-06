<?php

class SqlBuilderException extends \Exception {
}

class SQL {

  static function select(string ...$fields) {
    return (new SelectSQLBuilder)->select(...$fields);
  }

  static function eq(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, '=', $value);
  }

  static function dif(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, '<>', $value);
  }

  static function gt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, '>', $value);
  }

  static function gte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, '>=', $value);
  }

  static function lt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, '<', $value);
  }

  static function lte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, '<=', $value);
  }

  static function like(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, 'LIKE', $value);
  }

  static function ilike(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, 'ILIKE', $value);
  }

  static function between(string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
    return self::prepareTemplatesBetweenCondition($field, 'BETWEEN', $valueLess, $valueGreater);
  }

  static function notBetween(string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
    return self::prepareTemplatesBetweenCondition($field, 'NOT BETWEEN', $valueLess, $valueGreater);
  }

  static function in(string $field, string|int|float|SelectSQLBuilder $value, string|int|float ...$values) {
    if ($value instanceof SelectSQLBuilder) {
      $values = [$value];
    } else {
      array_unshift($values, $value);
    }

    return self::prepareTemplatesMultiValuesCondition($field, 'IN', ...$values);
  }

  static function notIn($field, $value, ...$values) {
    if ($value instanceof SelectSQLBuilder) {
      $values = [$value];
    } else {
      array_unshift($values, $value);
    }

    return self::prepareTemplatesMultiValuesCondition($field, 'NOT IN', ...$values);
  }

  static function isNull(string|int|float|SelectSQLBuilder $value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS NULL');
  }

  static function isNotNull($value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS NOT NULL');
  }

  static function isTrue($value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS TRUE');
  }

  static function isFalse($value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS FALSE');
  }

  static function isDistinctFrom(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, 'IS DISTINCT FROM', $value);
  }

  static function exists(SelectSQLBuilder $subSelect) {
    return self::prepareTemplatesRigthArgsCondition('EXISTS', $subSelect);
  }

  static function notExists(SelectSQLBuilder $subSelect) {
    return self::prepareTemplatesRigthArgsCondition('NOT EXISTS', $subSelect);
  }

  static function similarTo(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, 'SIMILAR TO', $value);
  }

  static function notSimilarTo(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRigthArgsCondition($field, 'NOT SIMILAR TO', $value);
  }

  static function sqlAnd(...$conditions) {
    return static::logical('AND', $conditions);
  }

  static function sqlOr(...$conditions) {
    return static::logical('OR', $conditions);
  }

  static function not(...$conditions) {
    return static::logical('NOT', $conditions);
  }

  private static function prepareTemplatesLeftRigthArgsCondition(string $field, string $operator, string|int|float|bool|SelectSQLBuilder $value) {
    $sqlTemplates = ["$field $operator ", ''];
    $params = [$value];

    if ($value instanceof SelectSQLBuilder) {
      $templates = $value->getAllTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];

      $sqlTemplates[0] = "$field $operator $sqlTemplates[0]";
    }

    return static::condition($sqlTemplates, $params);
  }

  private static function prepareTemplatesLeftArgsCondition(string|int|float|bool|SelectSQLBuilder $value, string $operator) {
    $sqlTemplates = [" $operator"];
    $params = [];

    if ($value instanceof SelectSQLBuilder) {
      $templates = $value->getAllTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];

      $index = array_key_last($sqlTemplates);

      $sqlTemplates[$index] = "$sqlTemplates[$index] $operator";
    } else {
      $sqlTemplates = ["$value $operator"];
    }

    return static::condition($sqlTemplates, $params);
  }

  private static function prepareTemplatesRigthArgsCondition(string $operator, string|int|float|bool|SelectSQLBuilder $value) {
    $sqlTemplates = ["$operator"];
    $params = [];

    if ($value instanceof SelectSQLBuilder) {
      $templates = $value->getAllTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];

      $sqlTemplates[0] = "$operator $sqlTemplates[0]";
    } else {
      $sqlTemplates = ["$operator $value"];
    }

    return static::condition($sqlTemplates, $params);
  }

  private static function prepareTemplatesBetweenCondition(string $field, string $operator, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
    $sqlTemplates = ["$field $operator ", ' AND ', ''];
    $params = [$valueLess, $valueGreater];

    if ($valueLess instanceof SelectSQLBuilder) {
      $templates = $valueLess->getAllTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];
      $params[] = [$valueGreater];

      $sqlTemplates[0] = "$field $operator $sqlTemplates[0]";
      $sqlTemplates[array_key_last($sqlTemplates)] = ") AND ";
      $sqlTemplates[] = "";
    }

    if ($valueGreater instanceof SelectSQLBuilder) {
      $templates = $valueGreater->getAllTemplatesWithParentheses();

      array_pop($sqlTemplates);
      array_pop($params);

      $templates['sqlTemplates'][0] = "{$sqlTemplates[array_key_last($sqlTemplates)]} {$templates['sqlTemplates'][0]}";

      array_pop($sqlTemplates);

      $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
      $params = array_merge($params, $templates['params']);
    }

    return static::condition($sqlTemplates, $params);
  }

  private static function prepareTemplatesMultiValuesCondition(string $field, string $operator, string|int|float|SelectSQLBuilder ...$values) {
    $sqlTemplates = ["$field $operator "];
    $params = [];

    $value = $values[0];

    if (isset($value) && $value instanceof SelectSQLBuilder) {
      $templates = $value->getAllTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];

      $sqlTemplates[0] = "$field $operator $sqlTemplates[0]";
    } else {
      foreach ($values as $value) {
        $sqlTemplates[] = '';
      }

      $sqlTemplates[0] .= "(";
      $sqlTemplates[array_key_last($sqlTemplates)] .= ')';
      $params = $values;
    }

    return static::condition($sqlTemplates, $params);
  }

  /**
   * @param (string|SelectSQLBuilder)[] $sqlTemplates
   * @return array{sqlTemplates: string|SelectSQLBuilder[], params: (string|number|boolean|null)[]}
   */
  private static function condition(array $sqlTemplates, array $params = []) {
    if (count($params) != count($sqlTemplates) - 1)
      throw new SqlBuilderException("Count");

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params
    ];
  }

  private static function logical(string $type, array $nested) {
    return [
      'type' => $type,
      'nested' => $nested
    ];
  }
}


abstract class SQLBuilder {

  /**
   * @var array<string,array{sqlTemplates: string|SelectSQLBuilder[], params: (string|number|boolean|null)[]}[]>
   */
  protected array $clausules = [];

  /**
   * @var array<string, string>
   */
  protected array $clausulesOrder = [];

  function build() {
    $template = $this->getAllTemplates();

    return [
      'sql' => implode(' ', $template['sqlTemplates']),
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
        $templatesMerged[] = $anterior . $separator . $primeiroAtual;
      }

      $templatesMerged = array_merge($templatesMerged, $array);
    }

    return $templatesMerged;
  }
}

class SQLConditionBuilder extends SQLBuilder {

  function __construct() {
    $this->clausules['WITH'] = [];
    $this->clausules['WHERE'] = [];
  }

  /**
   * @param array{sqlTemplates: string[], params: string|number|boolean|null[]}[] ...$conditions
   */
  function where(...$conditions) {
    $this->clausules['WHERE'] = array_merge($this->clausules['WHERE'], $conditions);

    return $this;
  }

  function getTemplateWhere() {
    return [
      'sqlTemplates' => [],
      'params' => [],
    ];
  }

  protected function buildCondition(array $condition) {
    if (isset($condition['nested'])) {
      $nestedConditions = array_map(function ($cond) {
        return $this->buildCondition($cond);
      }, $condition['nested']);

      if ($condition['type'] == 'NOT')
        return 'NOT (' . implode(' AND ', $nestedConditions) . ')';

      return '(' . implode(' ' . $condition['type'] . ' ', $nestedConditions) . ')';
    }

    return $condition;
  }
}

class SelectSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules['SELECT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['FROM'] = [];

    $this->clausulesOrder = [
      'SELECT' => 'getTemplateSelect',
      'WHERE' => 'getTemplateWhere',
    ];
  }

  function select(string ...$fields) {
    $this->clausules['SELECT'][0]['sqlTemplates'] = array_merge($this->clausules['SELECT'][0]['sqlTemplates'], $fields);

    return $this;
  }

  function from(string|SelectSQLBuilder $table, string $alias = '') {
    $sqlTemplates = [];
    $params = [];

    if ($table instanceof SelectSQLBuilder) {
      $sqlTemplates = $table->getAllTemplatesWithParentheses();

      $sqlTemplates = $sqlTemplates['sqlTemplates'];
      $params = $sqlTemplates['params'];
    }

    $this->clausules['FROM'] = [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];

    return $this;
  }

  function getTemplateSelect() {
    return [
      'sqlTemplates' => [],
      'params' => [],
    ];
  }

  function getTemplateFrom() {
    return [
      'sqlTemplates' => [],
      'params' => [],
    ];
  }
}

var_dump(
  SQL::select('name', 'id')
    ->from('users')
    ->where(
      SQL::eq('name', 'Dan'),
      SQL::sqlAnd(
        // SQL::in('name', SQL::select('id')->from('users'))
      ),
    )->build()
);

function console(...$args) {
?><script>
    console.log(...<?= json_encode($args) ?>)
  </script><?php
          }
