<?php

namespace App\Provider\QueryBuilder\FieldOperator;

use App\Provider\QueryBuilder\FilterOperator;

enum BooleanOperator: string {
  case EQUALS = FilterOperator::EQUALS->value;
  case DIFFERENT = FilterOperator::DIFFERENT->value;
  case FILLED = FilterOperator::FILLED->value;
}
