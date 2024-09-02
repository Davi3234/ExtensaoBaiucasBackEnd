<?php

class SelectSQLBuilder extends SQLBuilder {

  function fetchAllSqlTemplates(): array {
    return [
      'sqlTemplates' => ['SELECT * FROM user WHERE login = ', ''],
      'params' => ['Dan'],
    ];
  }
}