<?php

class SqlBuilderException extends \Exception {
}


class SQL {

  static function eq(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('=', $field, $value);
  }

  static function dif(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('<>', $field, $value);
  }

  static function gt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('>', $field, $value);
  }

  static function gte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('>=', $field, $value);
  }

  static function lt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('<', $field, $value);
  }

  static function lte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('<=', $field, $value);
  }

  static function like(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('LIKE', $field, $value);
  }

  static function ilike(string $field, string|int|float|bool|SelectSQLBuilder $value) {
    return self::prepareTemplatesSimpleCondition('ILIKE', $field, $value);
  }

  static function between(string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
    return self::prepareTemplatesBetweenCondition('BETWEEN', $field, $valueLess, $valueGreater);
  }

  static function notBetween(string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
    return self::prepareTemplatesBetweenCondition('NOT BETWEEN', $field, $valueLess, $valueGreater);
  }

  static function in(string $field, string|int|float|SelectSQLBuilder $value, string|int|float ...$values) {
    $sqlTemplates = ["$field IN ("];
    $params = [];

    if ($value instanceof SelectSQLBuilder)
      $values = [];

    array_unshift($values, $value);

    foreach ($values as $value) {
      $sqlTemplates[] = ', ';
    }

    $sqlTemplates[] = ')';

    return static::condition($sqlTemplates, $values);
  }

  static function notIn($field, $value, ...$values) {
    array_unshift($values, $value);

    $sqlTemplates = ["$field NOT IN ("];
    foreach ($values as $value) {
      $sqlTemplates[] = ', ';
    }
    $sqlTemplates[] = ')';

    return static::condition($sqlTemplates, $values);
  }

  static function isNull($field) {
    return static::condition(["$field IS NULL"]);
  }

  static function isNotNull($field) {
    return static::condition(["$field IS NOT NULL"]);
  }

  static function isTrue($field) {
    return static::condition(["$field IS TRUE"]);
  }

  static function isFalse($field) {
    return static::condition(["$field IS FALSE"]);
  }

  static function isDistinctFrom($field, $value) {
    return static::condition(["$field IS DISTINCT FROM $value"]);
  }

  static function exists($subSelect) {
    return static::condition(["EXISTS ($subSelect)"]);
  }

  static function notExists($subSelect) {
    return static::condition(["NOT EXISTS ($subSelect)"]);
  }

  static function similarTo($field, $value) {
    return static::condition(["$field SIMILAR TO $value"]);
  }

  static function notSimilarTo($field, $value) {
    return static::condition(["$field NOT SIMILAR TO $value"]);
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

  private static function prepareTemplatesSimpleCondition(string $operator, string $field, string|int|float|bool|SelectSQLBuilder $value) {
    $sqlTemplates = ["$field $operator ", ''];
    $params = [$value];

    if ($value instanceof SelectSQLBuilder) {
      $templates = $value->fetchAllSqlTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];

      $sqlTemplates[0] = "$field $operator $sqlTemplates[0]";
    }

    return static::condition($sqlTemplates, $params);
  }

  private static function prepareTemplatesBetweenCondition(string $operator, string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
    $sqlTemplates = ["$field $operator ", ' AND ', ''];
    $params = [$valueLess, $valueGreater];

    if ($valueLess instanceof SelectSQLBuilder) {
      $templates = $valueLess->fetchAllSqlTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];
      $params[] = [$valueGreater];

      $sqlTemplates[0] = "$field $operator $sqlTemplates[0]";
      $sqlTemplates[array_key_last($sqlTemplates)] = ") AND ";
      $sqlTemplates[] = "";
    }

    if ($valueGreater instanceof SelectSQLBuilder) {
      $templates = $valueGreater->fetchAllSqlTemplatesWithParentheses();

      array_pop($sqlTemplates);
      array_pop($params);

      $templates['sqlTemplates'][0] = "{$sqlTemplates[array_key_last($sqlTemplates)]} {$templates['sqlTemplates'][0]}";

      array_pop($sqlTemplates);

      $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
      $params = array_merge($params, $templates['params']);
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

  static function orderBy(...$orderByArgs) {
    $sql = '';

    if (count($orderByArgs) > 0)
      $sql = "ORDER BY " . implode(', ', $orderByArgs);

    return [
      'sql' => $sql,
      'clausule' => 'ORDERBY'
    ];
  }

  static function groupBy(...$groupByArgs) {
    $sql = '';

    if (count($groupByArgs) > 0)
      $sql = "GROUP BY " . implode(', ', $groupByArgs);

    return [
      'sql' => $sql,
      'clausule' => 'GROUPBY'
    ];
  }

  static function limit($limitArgs) {
    $sql = '';

    if (trim($limitArgs))
      $sql = "LIMIT $limitArgs";

    return [
      'sql' => $sql,
      'clausule' => 'LIMIT'
    ];
  }

  static function offset($offsetArgs) {
    $sql = '';

    if (trim($offsetArgs))
      $sql = "OFFSET $offsetArgs";

    return [
      'sql' => $sql,
      'clausule' => 'OFFSET'
    ];
  }
}


abstract class SQLBuilder {

  function __construct() {
  }

  /**
   * @var array<string,array{sqlTemplates: string|SelectSQLBuilder[], params: (string|number|boolean|null)[]}[]>
   */
  protected array $clausules = [];
  protected $params = [];

  function build() {
    return '';
  }

  function fetchAllSqlTemplatesWithParentheses() {
    $template = $this->fetchAllSqlTemplates();

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
  abstract function fetchAllSqlTemplates(): array;

  function createParam($value) {
    $this->params[] = $value;

    $number = count($this->params);

    return "$$number";
  }

  function getParams() {
    return $this->params;
  }
}

class SelectSQLBuilder extends SQLBuilder {

  function fetchAllSqlTemplates(): array {
    $templates = [
      'sqlTemplates' => ['SELECT * FROM user WHERE login = ', ''],
      'params' => ['Dan'],
    ];

    return [
      'sqlTemplates' => ['SELECT * FROM user WHERE login = ', ''],
      'params' => ['Dan'],
    ];
  }
}

printSQL(
  SQL::eq('name', 'Dan')
);

printSQL(
  SQL::dif('name', 'Dan')
);

printSQL(
  SQL::gt('name', 'Dan')
);

printSQL(
  SQL::gte('name', 'Dan')
);

printSQL(
  SQL::lt('name', 'Dan')
);

printSQL(
  SQL::lte('name', 'Dan')
);

printSQL(
  SQL::between('name', 1, 2)
);

printSQL(
  SQL::between('name', new SelectSQLBuilder, 2)
);

printSQL(
  SQL::between('name', 1, new SelectSQLBuilder)
);

printSQL(
  SQL::eq('name', new SelectSQLBuilder)
);

printSQL(
  SQL::dif('name', new SelectSQLBuilder)
);

printSQL(
  SQL::gt('name', new SelectSQLBuilder)
);

printSQL(
  SQL::gte('name', new SelectSQLBuilder)
);

printSQL(
  SQL::lt('name', new SelectSQLBuilder)
);

printSQL(
  SQL::lte('name', new SelectSQLBuilder)
);

printSQL(
  SQL::like('name', new SelectSQLBuilder)
);

printSQL(
  SQL::ilike('name', new SelectSQLBuilder)
);

printSQL(
  SQL::between('name', new SelectSQLBuilder, new SelectSQLBuilder)
);

function printSQL($sql) {
  var_dump($sql['sqlTemplates'], $sql['params']);
  echo '<br>';
}
