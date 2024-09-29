<?php

namespace App\Provider\QueryBuilder;

use App\Provider\QueryBuilder\FieldOperator\BooleanOperator;
use App\Provider\QueryBuilder\FieldOperator\DateOperator;
use App\Provider\QueryBuilder\FieldOperator\NumberOperator;
use App\Provider\QueryBuilder\FieldOperator\StringOperator;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\SQL;

class QueryBuilder {

  private SelectSQLBuilder $selectBuilder;

  /**
   * @param array<string, array{field: string, type: FieldType}> $filterMap
   */
  public function __construct(
    private readonly array $filterMap
  ) {
    $this->selectBuilder = SQL::select()->from('');
  }

  /**
   * @param array{field: string, value: mixed, operator: string}[] $filters
   */
  function parse(array $filters) {
    foreach ($filters as $filter) {
      $fieldFilter = trim($filter['field']);
      $operatorFilter = trim($filter['operator']);
      $value = $filter['value'];

      if (!$fieldFilter || !$operatorFilter)
        continue;

      $filterMap = $this->filterMap[$fieldFilter];

      if (!$filterMap || !$filterMap['type'])
        continue;

      $typeMap = $filterMap['type'];
      $fieldMap = $filterMap['field'] ?? $fieldFilter;

      $this->addCondition($fieldMap, $operatorFilter, $value, $typeMap);
    }
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   * @param FieldType $typeMap
   * @return void
   */
  function addCondition(string $field, string $operator, $value, FieldType $typeMap) {
    switch ($typeMap->value) {
      case FieldType::STRING->value:
        $this->addConditionString($field, $operator, $value);
        break;
      case FieldType::INTEGER->value:
      case FieldType::FLOAT->value:
        $this->addConditionNumber($field, $operator, $value);
        break;
      case FieldType::BOOLEAN->value:
        $this->addConditionBoolean($field, $operator, $value);
        break;
      case FieldType::DATE->value:
        $this->addConditionDate($field, $operator, $value);
        break;
    };
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   * @return void
   */
  function addConditionString(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionString($field, $operator, $value));
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   * @return void
   */
  function addConditionNumber(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionNumber($field, $operator, $value));
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   * @return void
   */
  function addConditionDate(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionDate($field, $operator, $value));
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   * @return void
   */
  function addConditionBoolean(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionBoolean($field, $operator, $value));
  }

  private function addConditionInSelectBuilder(?array $condition) {
    if (!$condition)
      return;

    $this->selectBuilder->where([$condition]);
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  static function getConditionString(string $field, string $operator, $value) {
    return match ($operator) {
      StringOperator::EQUALS->value => SQL::eq($field, $value),
      StringOperator::CONTAINS->value => SQL::like($field, '%' . str_replace(' ', '%', str_replace('  ', ' ', trim($value))) . '%'),
      StringOperator::NOT_CONTAINS->value => SQL::notLike($field, '%' . str_replace(' ', '%', str_replace('  ', ' ', trim($value))) . '%'),
      StringOperator::DIFFERENT->value => SQL::dif($field, $value),
      StringOperator::STARS_WITH->value => SQL::like($field, '%' . trim($value)),
      StringOperator::ENDS_WITH->value => SQL::like($field, trim($value) . '%'),
      StringOperator::FILLED->value => $value ? SQL::isNotNull($field) : SQL::isNull($field),
      default => null,
    };
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  static function getConditionNumber(string $field, string $operator, $value) {
    return match ($operator) {
      NumberOperator::EQUALS->value => SQL::eq($field, $value),
      NumberOperator::DIFFERENT->value => SQL::dif($field, $value),
      NumberOperator::CONTAINS->value => SQL::in($field, is_array($value) ? $value : [$value]),
      NumberOperator::NOT_CONTAINS->value => SQL::notIn($field, is_array($value) ? $value : [$value]),
      NumberOperator::GREATER_THAN->value => SQL::gt($field, $value),
      NumberOperator::GREATER_THAN_OR_EQUAL->value => SQL::gte($field, $value),
      NumberOperator::LESS_THAN->value => SQL::lt($field, $value),
      NumberOperator::LESS_THAN_OR_EQUAL->value => SQL::lte($field, $value),
      NumberOperator::FILLED->value => $value ? SQL::isNotNull($field) : SQL::isNull($field),
      default => null,
    };
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  static function getConditionDate(string $field, string $operator, $value) {
    return match ($operator) {
      DateOperator::EQUALS->value => SQL::eq($field, $value),
      DateOperator::DIFFERENT->value => SQL::dif($field, $value),
      DateOperator::GREATER_THAN->value => SQL::gt($field, $value),
      DateOperator::GREATER_THAN_OR_EQUAL->value => SQL::gte($field, $value),
      DateOperator::LESS_THAN->value => SQL::lt($field, $value),
      DateOperator::LESS_THAN_OR_EQUAL->value => SQL::lte($field, $value),
      DateOperator::FILLED->value => $value ? SQL::isNotNull($field) : SQL::isNull($field),
      default => null,
    };
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  static function getConditionBoolean(string $field, string $operator, $value) {
    return match ($operator) {
      BooleanOperator::EQUALS->value => $value ? SQL::isTrue($field) : SQL::isFalse($field),
      BooleanOperator::DIFFERENT->value => !$value ? SQL::isTrue($field) : SQL::isFalse($field),
      BooleanOperator::FILLED->value => $value ? SQL::isNotNull($field) : SQL::isNull($field),
      default => null,
    };
  }

  function getWhere() {
    return $this->selectBuilder->getWhere();
  }
}
