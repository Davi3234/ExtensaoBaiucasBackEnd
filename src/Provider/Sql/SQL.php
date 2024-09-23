<?php

namespace App\Provider\Sql;

class SQL extends SQLFormat {

  static function with(string $alias, SelectSQLBuilder $selectBuilder) {
    return (new SelectSQLBuilder)->with($alias, $selectBuilder);
  }

  static function select(string ...$fields) {
    return (new SelectSQLBuilder)->select(...$fields);
  }

  static function insertInto(string $table) {
    return (new InsertSQLBuilder)->insertInto($table);
  }

  static function update(string $table) {
    return (new UpdateSQLBuilder)->update($table);
  }

  static function deleteFrom(string $table) {
    return (new DeleteSQLBuilder)->deleteFrom($table);
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

  static function in(string $field, string|int|float|SelectSQLBuilder $value, array ...$values) {
    if ($value instanceof SelectSQLBuilder) {
      $values = [$value];
    } else {
      array_unshift($values, $value);
    }

    return self::prepareTemplatesMultiValuesCondition($field, 'IN', $values);
  }

  static function notIn(string $field, string|int|float|SelectSQLBuilder $value, array ...$values) {
    if ($value instanceof SelectSQLBuilder) {
      $values = [$value];
    } else {
      array_unshift($values, $value);
    }

    return self::prepareTemplatesMultiValuesCondition($field, 'NOT IN', $values);
  }

  static function isNull(string|int|float|SelectSQLBuilder $value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS NULL');
  }

  static function isNotNull(string|int|float|SelectSQLBuilder $value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS NOT NULL');
  }

  static function isTrue(string|int|float|SelectSQLBuilder $value) {
    return static::prepareTemplatesLeftArgsCondition($value, 'IS TRUE');
  }

  static function isFalse(string|int|float|SelectSQLBuilder $value) {
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

  static function sqlAnd(array ...$conditions) {
    return static::logical('AND', $conditions);
  }

  static function sqlOr(array ...$conditions) {
    return static::logical('OR', $conditions);
  }

  static function sqlNot(array ...$conditions) {
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

  private static function prepareTemplatesMultiValuesCondition(string $field, string $operator, array $values) {
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
