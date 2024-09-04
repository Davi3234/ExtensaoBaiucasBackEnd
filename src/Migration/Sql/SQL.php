<?php

class SqlBuilderException extends \Exception {
}


class SQL {

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
    }
    else {
      array_unshift($values, $value);
    }

    return self::prepareTemplatesMultiValuesCondition($field, 'IN', ...$values);
  }

  static function notIn($field, $value, ...$values) {
    if ($value instanceof SelectSQLBuilder) {
      $values = [$value];
    }
    else {
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
      $templates = $value->fetchAllSqlTemplatesWithParentheses();

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
      $templates = $value->fetchAllSqlTemplatesWithParentheses();

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
      $templates = $value->fetchAllSqlTemplatesWithParentheses();

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

  private static function prepareTemplatesMultiValuesCondition(string $field, string $operator, string|int|float|SelectSQLBuilder ...$values) {
    $sqlTemplates = ["$field $operator "];
    $params = $values;

    $value = $values[0];

    if (isset($value) && $value instanceof SelectSQLBuilder) {
      $templates = $value->fetchAllSqlTemplatesWithParentheses();

      $sqlTemplates = $templates['sqlTemplates'];
      $params = $templates['params'];

      $sqlTemplates[0] = "$field $operator $sqlTemplates[0]";
    } else {
      foreach($values as $value) {
        $sqlTemplates[] = '';
      }

      $sqlTemplates[0] .= "(";
      $sqlTemplates[array_key_last($sqlTemplates)] .= ')';
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
    ];
  }

  static function groupBy(...$groupByArgs) {
    $sql = '';

    if (count($groupByArgs) > 0)
      $sql = "GROUP BY " . implode(', ', $groupByArgs);

    return [
      'sql' => $sql,
    ];
  }

  static function limit($limitArgs) {
    $sql = '';

    if (trim($limitArgs))
      $sql = "LIMIT $limitArgs";

    return [
      'sql' => $sql,
    ];
  }

  static function offset($offsetArgs) {
    $sql = '';

    if (trim($offsetArgs))
      $sql = "OFFSET $offsetArgs";

    return [
      'sql' => $sql,
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
  SQL::isNull('name')
);

printSQL(
  SQL::in('id', 1, 2, 3, 4)
);

printSQL(
  SQL::notIn('id', 1, 2, 3, 4)
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

printSQL(
  SQL::isNull(new SelectSQLBuilder)
);

printSQL(
  SQL::exists(new SelectSQLBuilder)
);

printSQL(
  SQL::in('id', new SelectSQLBuilder)
);

printSQL(
  SQL::notIn('id', new SelectSQLBuilder)
);

function printSQL($sql) {
  var_dump($sql['sqlTemplates'], $sql['params']);
  echo '<br>';
}
