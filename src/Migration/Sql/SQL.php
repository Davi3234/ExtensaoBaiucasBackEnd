<?php

namespace App\Migration\Sql;

class SqlBuilderException extends \Exception {
}

class SQL {

  static function select(string ...$fields) {
    return (new SelectSQLBuilder)->select(...$fields);
  }

  static function eq(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, '=', $value);
  }

  static function dif(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, '<>', $value);
  }

  static function gt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, '>', $value);
  }

  static function gte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, '>=', $value);
  }

  static function lt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, '<', $value);
  }

  static function lte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, '<=', $value);
  }

  static function like(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, 'LIKE', $value);
  }

  static function ilike(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, 'ILIKE', $value);
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
    return self::prepareTemplatesLeftRightArgsCondition($field, 'IS DISTINCT FROM', $value);
  }

  static function exists(SelectSQLBuilder $subSelect) {
    return self::prepareTemplatesRightArgsCondition('EXISTS', $subSelect);
  }

  static function notExists(SelectSQLBuilder $subSelect) {
    return self::prepareTemplatesRightArgsCondition('NOT EXISTS', $subSelect);
  }

  static function similarTo(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, 'SIMILAR TO', $value);
  }

  static function notSimilarTo(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesLeftRightArgsCondition($field, 'NOT SIMILAR TO', $value);
  }

  static function sqlAnd(...$conditions) {
    return static::logical('AND', $conditions);
  }

  static function sqlOr(...$conditions) {
    return static::logical('OR', $conditions);
  }

  static function sqlNot(...$conditions) {
    return static::logical('NOT', $conditions);
  }

  private static function prepareTemplatesLeftRightArgsCondition(string $field, string $operator, string|int|float|bool|SelectSQLBuilder $value) {
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

  private static function prepareTemplatesRightArgsCondition(string $operator, string|int|float|bool|SelectSQLBuilder $value) {
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
      foreach ($values as $key => $value) {
        if ($key < count($values) - 1)
          $sqlTemplates[] = ', ';
        else
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

  protected function getTemplateWhere() {
    $sqlTemplates = ['1 = 1'];
    $params = [];

    foreach ($this->clausules['WHERE'] as $template) {
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

class SelectSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules['SELECT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['FROM'] = [];
    $this->clausules['ORDERBY'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['LIMIT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['OFFSET'] = [['sqlTemplates' => [], 'params' => []]];

    $this->clausulesOrder = [
      'SELECT' => 'getTemplateSelect',
      'FROM' => 'getTemplateFrom',
      'WHERE' => 'getTemplateWhere',
      'ORDERBY' => 'getTemplateOrderBy',
      'LIMIT' => 'getTemplateLimit',
      'OFFSET' => 'getTemplateOffset',
    ];
  }

  function select(string ...$fields) {
    $this->clausules['SELECT'][0]['sqlTemplates'] = array_merge($this->clausules['SELECT'][0]['sqlTemplates'], $fields);

    return $this;
  }

  function from(string|SelectSQLBuilder $table, string $alias = '') {
    $this->clausules['FROM'][0] = [
      'sqlTemplates' => [$table, $alias],
      'params' => [],
    ];

    return $this;
  }

  function orderBy(string|int ...$values) {
    $sqlTemplate = $this->clausules['ORDERBY'][0]['sqlTemplates'];

    foreach ($values as $value) {
      $sqlTemplate[] = '';
    }

    $this->clausules['ORDERBY'][0]['sqlTemplates'] = $sqlTemplate;
    $this->clausules['ORDERBY'][0]['params'] = array_merge($this->clausules['ORDERBY'][0]['params'], $values);

    return $this;
  }

  function limit(string|int $value) {
    $this->clausules['LIMIT'][0] = ['sqlTemplates' => [''], 'params' => [$value]];

    return $this;
  }

  function offset(string|int $value) {
    $this->clausules['OFFSET'][0] = ['sqlTemplates' => [''], 'params' => [$value]];

    return $this;
  }

  protected function getTemplateSelect() {
    $sqlTemplates = $this->clausules['SELECT'][0]['sqlTemplates'];
    $params = $this->clausules['SELECT'][0]['params'];

    if (!$sqlTemplates)
      $sqlTemplates = ['*'];

    return [
      'sqlTemplates' => ['SELECT ' . implode(', ', $sqlTemplates)],
      'params' => $params,
    ];
  }

  protected function getTemplateFrom() {
    $sqlTemplates = [];
    $params = $this->clausules['FROM'][0]['params'];

    foreach ($this->clausules['FROM'][0]['sqlTemplates'] as $template) {
      if ($template instanceof SelectSQLBuilder) {
        $templates = $template->getAllTemplatesWithParentheses();

        $sqlTemplates = $this->merge_templates(' ', $sqlTemplates, $templates['sqlTemplates']);
        $params = array_merge($params, $templates['params']);
      } else {
        $sqlTemplates = $this->merge_templates(' ', $sqlTemplates, [$template]);
      }
    }

    return [
      'sqlTemplates' => $this->merge_templates(' ', ['FROM'], $sqlTemplates),
      'params' => $params,
    ];
  }

  protected function getTemplateOrderBy() {
    $sqlTemplates = $this->clausules['ORDERBY'][0]['sqlTemplates'];
    $params = $this->clausules['ORDERBY'][0]['params'];

    if (!$sqlTemplates)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    foreach ($sqlTemplates as $key => &$template) {
      if ($key > 0)
        $template = ',';
    }

    $sqlTemplates[] = '';
    $sqlTemplates[0] = "ORDER BY $sqlTemplates[0]";

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  protected function getTemplateLimit() {
    $params = $this->clausules['LIMIT'][0]['params'];

    if (!$params)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    return [
      'sqlTemplates' => ['LIMIT ', ''],
      'params' => $params,
    ];
  }

  protected function getTemplateOffset() {
    $params = $this->clausules['OFFSET'][0]['params'];

    if (!$params)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    return [
      'sqlTemplates' => ['OFFSET ', ''],
      'params' => $params,
    ];
  }
}

$sqlBuilder = SQL::select('name', 'id')
  ->from('"user"', 'us')
  ->where(
    SQL::eq('name', 'Dan'),
    SQL::sqlNot(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn('id', SQL::select('id')->from('"user"')->where(SQL::eq('type', 'ADM')))
    ),
    SQL::sqlAnd(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn('id', SQL::select('id')->from('"user"')->where(SQL::eq('type', 'ADM')))
    ),
    SQL::sqlOr(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn('id', SQL::select('id')->from('"user"')->where(SQL::eq('type', 'ADM')))
    )
  )
  ->orderBy(1, 'id')
  ->orderBy(3)
  ->limit(1)
  ->offset(2)
  ->select('login');

$sqlResult = $sqlBuilder->build();

consoleSQL($sqlResult);

function consoleSQL($args) {
  console($args['sql'], $args['params']);
}

function console(...$args) {
?><script>
    console.log(...<?= json_encode($args) ?>)
  </script><?php
          }
