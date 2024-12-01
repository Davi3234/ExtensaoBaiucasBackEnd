<?php

namespace Provider\Database\Enums;

enum Driver: string {
  case MYSQL = 'pdo_mysql';
  case PGSQL  = 'pdo_pgsql';
  case SQLITE = 'pdo_sqlite';
}
