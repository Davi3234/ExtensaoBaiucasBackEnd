<?php

namespace App\Provider\QueryBuilder;

enum FieldType: string {
  case FLOAT = 'FLOAT';
  case INTEGER = 'INTEGER';
  case BOOLEAN = 'BOOLEAN';
  case STRING = 'STRING';
  case DATE = 'DATE';
  case ENUM = 'ENUM';
}
