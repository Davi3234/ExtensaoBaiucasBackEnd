<?php

namespace App\Provider\Sql;

class ReturningSQLBuilder extends SQLBuilder {

  function __construct() {
    $this->clausules['RETURNING'] = [];
  }

  function returning(string ...$fields) {
    $this->clausules['RETURNING'] = array_merge($this->clausules['RETURNING'], $fields);

    return $this;
  }

  protected function getTemplateReturning() {
    return getTemplateReturning($this->clausules["RETURNING"]);
  }
}

class ReturningConditionSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    $this->clausules['RETURNING'] = [];
  }

  function returning(string ...$fields) {
    $this->clausules['RETURNING'] = array_merge($this->clausules['RETURNING'], $fields);

    return $this;
  }

  protected function getTemplateReturning() {
    return getTemplateReturning($this->clausules["RETURNING"]);
  }
}

function getTemplateReturning($sqlReturning) {
  if (!$sqlReturning)
    return [
      'sqlTemplates' => [],
      'params' => [],
    ];

  $sqlReturning = implode(', ', $sqlReturning);

  return [
    'sqlTemplates' => ["RETURNING $sqlReturning"],
    'params' => [],
  ];
}
