<?php

namespace App\Provider\QueryBuilder;

use App\Provider\Sql\SQL;
use App\Provider\Zod\Z;
use App\Provider\QueryBuilder\FieldOperator\BooleanOperator;
use App\Provider\QueryBuilder\FieldOperator\StringOperator;
use App\Provider\QueryBuilder\FieldOperator\NumberOperator;
use App\Provider\QueryBuilder\FieldOperator\DateOperator;

class QueryBuilder {

  /**
   * @var array{sqlTemplates: string[], params: string|number|boolean|null[]}[]
   */
  private array $conditions = [];

  /**
   * @var string[]
   */
  private array $orderBy = [];

  /**
   * @param array<string, array{field: string, type: FieldType}> $filterMap
   * @param string[]|array<array-key, string> $orderMap
   */
  public function __construct(
    private readonly array $filterMap = [],
    private readonly array $orderMap = []
  ) {
  }

  function toSchema() {
    return Z::object([
      'filters' => $this->toSchemaFilter(),
      'orderBy' => $this->toSchemaOrderBy(),
    ])->coerce()->toArray()->defaultValue([]);
  }

  function toSchemaFilter() {
    return Z::arrayZod(
      Z::object([
        'field' => Z::string(),
        'value' => Z::mixed(),
        'operator' => Z::string(),
      ])
        ->coerce()
        ->toArray()
    )
      ->defaultValue([])
      ->filter(function ($value) {
        if (!$value['field'] || !$value['operator'])
          return false;

        return FilterOperator::tryFrom($value['operator']) != null;
      });
  }

  function toSchemaOrderBy() {
    $orders = [];

    foreach (array_keys($this->orderMap) as $name) {
      $orders[$name] = Z::string()->toUpperCase()->transform(fn($order) => $order == 'DESC' ? $order : 'ASC');
    }

    return Z::object($orders)
      ->coerce()
      ->toArray();
  }

  /**
   * @param array{field: string, value: mixed, operator: string}[] $filters
   * @param array<string, string> $orders
   */
  function parse(array $filters = [], array $orders = [], array $ordersDefault = []) {
    $conditions = $this->parseFilters($filters);
    $orderBy = $this->parseOrders($orders, $ordersDefault);

    return [
      'conditions' => $conditions,
      'orderBy' => $orderBy,
    ];
  }

  /**
   * @param array{field: string, value: mixed, operator: string}[] $filters
   */
  function parseFilters(array $filters = []) {
    foreach ($filters as $filter)
      $this->resolveFilter($filter);

    return $this->getWhere();
  }

  /**
   * @param array{field: string, value: mixed, operator: string} $filter
   */
  private function resolveFilter(array $filter) {
    $fieldFilter = trim($filter['field']);
    $operatorFilter = trim($filter['operator']);
    $value = $filter['value'];

    if (!$fieldFilter || !$operatorFilter)
      return;

    $filterMap = $this->filterMap[$fieldFilter];

    if (!$filterMap || !$filterMap['type'])
      return;

    $typeMap = $filterMap['type'];
    $fieldMap = $filterMap['field'] ?? $fieldFilter;

    $this->addCondition($fieldMap, $operatorFilter, $value, $typeMap);
  }

  /**
   * @param array<string, string> $orders
   * @param array<string, string> $ordersDefault
   */
  function parseOrders(array $orders = [], array $ordersDefault = []) {
    foreach ($orders as $name => $order)
      $this->resolveOrderBy($name, $order);

    foreach ($ordersDefault as $name => $order)
      $this->resolveOrderBy($name, $order);

    return $this->getOrderBy();
  }

  private function resolveOrderBy($name, $order) {
    if (!$name || !array_key_exists($name, $this->orderMap))
      return;

    $orderMap = $this->orderMap[$name];

    $this->addOrderBy((string) $orderMap, strtoupper((string) $order));
  }

  function addOrderBy(string $orderBy, string $order = 'ASC') {
    $this->orderBy[] = "$orderBy $order";
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   * @param FieldType $typeMap
   */
  function addCondition(string $field, string $operator, $value, FieldType $typeMap) {
    switch ($typeMap) {
      case FieldType::STRING:
        $this->addConditionString($field, $operator, $value);
        break;
      case FieldType::INTEGER:
      case FieldType::FLOAT:
        $this->addConditionNumber($field, $operator, $value);
        break;
      case FieldType::BOOLEAN:
        $this->addConditionBoolean($field, $operator, $value);
        break;
      case FieldType::DATE:
        $this->addConditionDate($field, $operator, $value);
        break;
    };
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  function addConditionString(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionString($field, $operator, $value));
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  function addConditionNumber(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionNumber($field, $operator, $value));
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  function addConditionDate(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionDate($field, $operator, $value));
  }

  /**
   * @param string $field
   * @param string $operator
   * @param mixed $value
   */
  function addConditionBoolean(string $field, string $operator, $value) {
    $this->addConditionInSelectBuilder(self::getConditionBoolean($field, $operator, $value));
  }

  private function addConditionInSelectBuilder(?array $condition) {
    if (!$condition)
      return;

    $this->conditions[] = $condition;
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
    return $this->conditions;
  }

  function getOrderBy() {
    return $this->orderBy;
  }
}
