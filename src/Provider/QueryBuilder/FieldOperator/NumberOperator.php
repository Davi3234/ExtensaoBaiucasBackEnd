<?php

namespace App\Provider\QueryBuilder\FieldOperator;

use App\Provider\QueryBuilder\FilterOperator;

enum NumberOperator: string {
  case EQUALS = FilterOperator::EQUALS->value;
  case DIFFERENT = FilterOperator::DIFFERENT->value;
  case CONTAINS = FilterOperator::CONTAINS->value;
  case NOT_CONTAINS = FilterOperator::NOT_CONTAINS->value;
  case GREATER_THAN = FilterOperator::GREATER_THAN->value;
  case GREATER_THAN_OR_EQUAL = FilterOperator::GREATER_THAN_OR_EQUAL->value;
  case LESS_THAN = FilterOperator::LESS_THAN->value;
  case LESS_THAN_OR_EQUAL = FilterOperator::LESS_THAN_OR_EQUAL->value;
  case FILLED = FilterOperator::FILLED->value;
}
