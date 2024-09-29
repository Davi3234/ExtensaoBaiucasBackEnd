<?php

use App\Provider\QueryBuilder\QueryBuilder;
use App\Provider\QueryBuilder\FieldType;

$map = [
  'name' => ['field' => 'us.name', 'type' => FieldType::STRING],
  'id' => ['field' => 'us.id', 'type' => FieldType::INTEGER],
];

$queryBuilder = new QueryBuilder($map);

$filters = [
  ['field' => 'id', 'value' => [3, 4, 5], 'operator' => 'in'],
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'eq'],
];
