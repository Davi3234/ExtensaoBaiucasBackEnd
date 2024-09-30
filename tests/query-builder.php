<?php

use App\Provider\QueryBuilder\QueryBuilder;
use App\Provider\QueryBuilder\FieldType;
use App\Provider\Sql\SQL;

$filters = [
  // String
  ['field' => 'name', 'value' => 'Fulano de Tal', 'operator' => 'eq'],
  ['field' => 'name', 'value' => 'Fulano de Tal', 'operator' => 'in'],
  ['field' => 'name', 'value' => 'Fulano de Tal', 'operator' => 'nin'],
  ['field' => 'name', 'value' => 'Fulano de Tal', 'operator' => 'dif'],
  ['field' => 'name', 'value' => 'Fulano de Tal', 'operator' => 'sw'],
  ['field' => 'name', 'value' => 'Fulano de Tal', 'operator' => 'ew'],
  ['field' => 'name', 'value' => true, 'operator' => 'fil'],
  ['field' => 'name', 'value' => false, 'operator' => 'fil'],
  // Integer
  ['field' => 'id', 'value' => 1, 'operator' => 'eq'],
  ['field' => 'id', 'value' => 1, 'operator' => 'dif'],
  ['field' => 'id', 'value' => [1, 2, 3], 'operator' => 'in'],
  ['field' => 'id', 'value' => [1, 2, 3], 'operator' => 'nin'],
  ['field' => 'id', 'value' => 1, 'operator' => 'gt'],
  ['field' => 'id', 'value' => 1, 'operator' => 'gte'],
  ['field' => 'id', 'value' => 1, 'operator' => 'lt'],
  ['field' => 'id', 'value' => 1, 'operator' => 'lte'],
  ['field' => 'id', 'value' => true, 'operator' => 'fil'],
  ['field' => 'id', 'value' => false, 'operator' => 'fil'],
  // Date
  ['field' => 'createAt', 'value' => '10-09-2024', 'operator' => 'eq'],
  ['field' => 'createAt', 'value' => '10-09-2024', 'operator' => 'dif'],
  ['field' => 'createAt', 'value' => '10-09-2024', 'operator' => 'gt'],
  ['field' => 'createAt', 'value' => '10-09-2024', 'operator' => 'gte'],
  ['field' => 'createAt', 'value' => '10-09-2024', 'operator' => 'lt'],
  ['field' => 'createAt', 'value' => '10-09-2024', 'operator' => 'lte'],
  // Boolean
  ['field' => 'active', 'value' => true, 'operator' => 'eq'],
  ['field' => 'active', 'value' => false, 'operator' => 'eq'],
  ['field' => 'active', 'value' => true, 'operator' => 'dif'],
  ['field' => 'active', 'value' => false, 'operator' => 'dif'],
  ['field' => 'active', 'value' => true, 'operator' => 'fil'],
  ['field' => 'active', 'value' => false, 'operator' => 'fil'],
];

$orderBy = [
  'id' => 'DESC',
  'name' => 'ASC',
  'active' => 'ASC',
];

$queryBuilder = new QueryBuilder(
  filterMap: [
    'name' => ['field' => 'us.name', 'type' => FieldType::STRING],
    'id' => ['field' => 'us.id', 'type' => FieldType::INTEGER],
    'createAt' => ['field' => 'us.create_at', 'type' => FieldType::DATE],
    'active' => ['field' => 'us.active', 'type' => FieldType::BOOLEAN],
  ],
  orderMap: [
    'name' => 'us.name',
    'id' => 'us.id',
  ]
);

$querySchema = $queryBuilder->toSchema();

$dto = $querySchema->parseNoSafe(['filters' => $filters, 'orderBy' => $orderBy]);

$queryFilters = $queryBuilder->parse($dto['filters'], $dto['orderBy'], ['id' => 'ASC']);

$sql = SQL::select()->from('users')->where($queryFilters['conditions'])->orderBy($queryFilters['orderBy'])->build();

print_r($sql);
