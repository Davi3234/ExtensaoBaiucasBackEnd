<?php

namespace App\Provider\QueryBuilder;

enum FilterOperator: string {
  case EQUALS = 'eq';
  case DIFFERENT = 'dif';
  case CONTAINS = 'in';
  case NOT_CONTAINS = 'nin';
  case FILLED = 'fil';
  case LESS_THAN = 'lt';
  case LESS_THAN_OR_EQUAL = 'lte';
  case GREATER_THAN = 'gt';
  case GREATER_THAN_OR_EQUAL = 'gte';
  case STARS_WITH = 'sw';
  case ENDS_WITH = 'ew';
}
