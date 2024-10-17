<?php

namespace App\Provider\QueryBuilder\FieldOperator;

use App\Provider\QueryBuilder\FilterOperator;

enum StringOperator: string {
  case EQUALS = FilterOperator::EQUALS->value;
  case CONTAINS = FilterOperator::CONTAINS->value;
  case NOT_CONTAINS = FilterOperator::NOT_CONTAINS->value;
  case DIFFERENT = FilterOperator::DIFFERENT->value;
  case STARS_WITH = FilterOperator::STARS_WITH->value;
  case ENDS_WITH = FilterOperator::ENDS_WITH->value;
  case FILLED = FilterOperator::FILLED->value;
}
