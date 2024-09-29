<?php

namespace App\Provider\Sql;

class ReturningSQLBuilder extends SQLBuilder {

  function __construct() {
    $this->clauses['RETURNING'] = [];
  }

  function returning(string ...$fields) {
    $this->clauses['RETURNING'] = array_merge($this->clauses['RETURNING'], $fields);

    return $this;
  }

  protected function getTemplateReturning() {
    return getTemplateReturning($this->clauses["RETURNING"]);
  }
}

class ReturningConditionSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    $this->clauses['RETURNING'] = [];
  }

  function returning(string ...$fields) {
    $this->clauses['RETURNING'] = array_merge($this->clauses['RETURNING'], $fields);

    return $this;
  }

  protected function getTemplateReturning() {
    return getTemplateReturning($this->clauses["RETURNING"]);
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
