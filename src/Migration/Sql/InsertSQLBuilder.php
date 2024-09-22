<?php

namespace App\Migration\Sql;

class InsertSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    parent::__construct();

    $this->clausulesOrder = [];
  }
}
