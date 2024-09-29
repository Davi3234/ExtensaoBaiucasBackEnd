<?php

use App\Provider\QueryBuilder\QueryBuilder;
use App\Provider\QueryBuilder\FieldType;
use App\Provider\Sql\SQL;

$queryBuilder = new QueryBuilder([
  'name' => ['field' => 'us.name', 'type' => FieldType::STRING],
  'id' => ['field' => 'us.id', 'type' => FieldType::INTEGER],
  'createAt' => ['field' => 'us.create_at', 'type' => FieldType::DATE],
  'active' => ['field' => 'us.active', 'type' => FieldType::BOOLEAN],
]);

$filters = [
  // String
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'eq'],
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'in'],
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'nin'],
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'dif'],
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'sw'],
  ['field' => 'name', 'value' => 'Dan Ruan', 'operator' => 'ew'],
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

$queryBuilder->parse($filters);

$conditions = $queryBuilder->getWhere();

SQL::select()->from('users')->where($conditions);
// "SELECT * FROM users WHERE 1 = 1 AND us.name = $1 AND us.name LIKE $2 AND us.name NOT LIKE $3 AND us.name <> $4 AND us.name LIKE $5 AND us.name LIKE $6 AND us.name IS NOT NULL AND us.name IS NULL AND us.id = $7 AND us.id <> $8 AND us.id IN ( $9 ,  $10 ,  $11 ,  $12 ) AND us.id NOT IN ( $13 ,  $14 ,  $15 ,  $16 ) AND us.id > $17 AND us.id >= $18 AND us.id < $19 AND us.id <= $20 AND us.id IS NOT NULL AND us.id IS NULL AND us.create_at = $21 AND us.create_at <> $22 AND us.create_at > $23 AND us.create_at >= $24 AND us.create_at < $25 AND us.create_at <= $26 AND us.active IS TRUE AND us.active IS FALSE AND us.active IS FALSE AND us.active IS TRUE AND us.active IS NOT NULL AND us.active IS NULL"
// ["Dan Ruan", "%Dan%Ruan%", "%Dan%Ruan%", "Dan Ruan", "%Dan Ruan", "Dan Ruan%", 1, 1, 1, 1, 2, 3, 1, 1, 2, 3, 1, 1, 1, 1, "10-09-2024", "10-09-2024", "10-09-2024", "10-09-2024", "10-09-2024", "10-09-2024"]