<?php

namespace App\Provider\Sql;

class InsertSQLBuilder extends ReturningSQLBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules["INTO"] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules["PARAMS"] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules["VALUES"] = [];

    $this->clausulesOrder = [
      'INTO' => 'getTemplateInto',
      'PARAMS' => 'getTemplateParams',
      'VALUES' => 'getTemplateValue',
      'RETURNING' => 'getTemplateReturning',
    ];
  }

  function insertInto(string $table) {
    $this->clausules["INTO"][0]['sqlTemplates'] = [$table];

    return $this;
  }

  function params(string ...$params) {
    $this->clausules["PARAMS"][0]['sqlTemplates'] = array_merge($this->clausules["PARAMS"][0]['sqlTemplates'], $params);

    return $this;
  }

  /**
   * @param array<string, string|int|float|bool>[] $values
   */
  function values(array ...$values) {
    foreach ($values as $value) {
      $paramsName = [];
      $paramsSql = [];

      foreach ($value as $key => $valueParam) {
        $paramsName[$key] = $key;
        $paramsSql[$key] = $valueParam;
      }

      $this->clausules["VALUES"][] = [
        'sqlTemplates' => $paramsName,
        'params' => $paramsSql
      ];
    }

    return $this;
  }

  protected function getTemplateInto() {
    $sqlParams = $this->clausules["INTO"][0]['sqlTemplates'];

    if (!$sqlParams || !$sqlParams[0]) {
      throw new SqlBuilderException('Table name not defined for clause "INSERT INTO"');
    }

    return [
      'sqlTemplates' => ["INSERT INTO $sqlParams[0]"],
      'params' => [],
    ];
  }

  protected function getTemplateParams() {
    $sqlParams = $this->getParametersName();

    if (!$sqlParams) {
      throw new SqlBuilderException('Params not defined for clause "INSERT INTO"');
    }

    return [
      'sqlTemplates' => ['(' . implode(', ', $sqlParams) . ')'],
      'params' => [],
    ];
  }

  protected function getTemplateValue() {
    $sqlValues = $this->clausules["VALUES"];

    if (!$sqlValues) {
      throw new SqlBuilderException('Values not defined for clause "INSERT INTO"');
    }

    $sqlParams = $this->getParametersName();

    $sqlTemplates = [];
    $params = [];

    foreach ($sqlValues as $sqlValue) {
      $sqlTemplate = [];

      foreach ($sqlParams as $param) {
        $paramName = $sqlValue['sqlTemplates'][$param];
        $value = $sqlValue['params'][$param];

        $sqlTemplate = self::merge_templates(', ', $sqlTemplate, ["$paramName = ", '']);
        $params[] = $value;
      }

      $sqlTemplate[0] = "($sqlTemplate[0]";
      $sqlTemplate[array_key_last($sqlTemplate)] .= ')';

      $sqlTemplates = self::merge_templates(', ', $sqlTemplates, $sqlTemplate);
    }

    $sqlTemplates[0] = "VALUES $sqlTemplates[0]";

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  private function getParametersName() {
    $sqlParams = $this->clausules["PARAMS"][0]['sqlTemplates'];

    return $sqlParams;
  }
}

$sqlBuilder = SQL::insertInto('"user"')
  ->params('name', 'login')
  ->values(
    [
      'name' => 'Dan',
      'login' => 'dan@gmail.com',
    ],
    [
      'login' => 'davi@gmail.com',
      'name' => 'Davi',
    ]
  )
  ->returning('id', 'name');
