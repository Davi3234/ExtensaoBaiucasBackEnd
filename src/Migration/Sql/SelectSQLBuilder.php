<?php

namespace App\Migration\Sql;

class SelectSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    parent::__construct();

    $this->clausules['SELECT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['FROM'] = [];
    $this->clausules['ORDERBY'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['LIMIT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clausules['OFFSET'] = [['sqlTemplates' => [], 'params' => []]];

    $this->clausulesOrder = [
      'SELECT' => 'getTemplateSelect',
      'FROM' => 'getTemplateFrom',
      'WHERE' => 'getTemplateWhere',
      'ORDERBY' => 'getTemplateOrderBy',
      'LIMIT' => 'getTemplateLimit',
      'OFFSET' => 'getTemplateOffset',
    ];
  }

  function select(string ...$fields) {
    $this->clausules['SELECT'][0]['sqlTemplates'] = array_merge($this->clausules['SELECT'][0]['sqlTemplates'], $fields);

    return $this;
  }

  function from(string|SelectSQLBuilder $table, string $alias = '') {
    $this->clausules['FROM'][0] = [
      'sqlTemplates' => [$table, $alias],
      'params' => [],
    ];

    return $this;
  }

  function orderBy(string|int ...$values) {
    $sqlTemplate = $this->clausules['ORDERBY'][0]['sqlTemplates'];

    foreach ($values as &$value) {
      $value = is_string($value) ? trim($value) : (string) $value;

      [$column, $direction] = explode(' ', $value);

      $direction = strtoupper($direction);

      if ($direction != 'DESC') {
        $direction = 'ASC';
      }

      $value = trim($column);
      $sqlTemplate[] = $direction;
    }

    $this->clausules['ORDERBY'][0]['sqlTemplates'] = $sqlTemplate;
    $this->clausules['ORDERBY'][0]['params'] = array_merge($this->clausules['ORDERBY'][0]['params'], $values);

    return $this;
  }

  function limit(string|int $value) {
    $this->clausules['LIMIT'][0] = ['sqlTemplates' => [''], 'params' => [$value]];

    return $this;
  }

  function offset(string|int $value) {
    $this->clausules['OFFSET'][0] = ['sqlTemplates' => [''], 'params' => [$value]];

    return $this;
  }

  protected function getTemplateSelect() {
    $sqlTemplates = $this->clausules['SELECT'][0]['sqlTemplates'];
    $params = $this->clausules['SELECT'][0]['params'];

    if (!$sqlTemplates)
      $sqlTemplates = ['*'];

    return [
      'sqlTemplates' => ['SELECT ' . implode(', ', $sqlTemplates)],
      'params' => $params,
    ];
  }

  protected function getTemplateFrom() {
    $sqlTemplates = [];
    $params = $this->clausules['FROM'][0]['params'];

    foreach ($this->clausules['FROM'][0]['sqlTemplates'] as $template) {
      if ($template instanceof SelectSQLBuilder) {
        $templates = $template->getAllTemplatesWithParentheses();

        $sqlTemplates = $this->merge_templates(' ', $sqlTemplates, $templates['sqlTemplates']);
        $params = array_merge($params, $templates['params']);
      } else {
        $sqlTemplates = $this->merge_templates(' ', $sqlTemplates, [$template]);
      }
    }

    return [
      'sqlTemplates' => $this->merge_templates(' ', ['FROM'], $sqlTemplates),
      'params' => $params,
    ];
  }

  protected function getTemplateOrderBy() {
    $sqlTemplates = $this->clausules['ORDERBY'][0]['sqlTemplates'];
    $params = $this->clausules['ORDERBY'][0]['params'];

    if (!$sqlTemplates)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    foreach ($sqlTemplates as $key => &$template) {
      $template = " $template";

      if ($key < count($sqlTemplates) - 1) {
        $template .= ',';
      }
    }

    array_unshift($sqlTemplates, "ORDER BY ");

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  protected function getTemplateLimit() {
    $params = $this->clausules['LIMIT'][0]['params'];

    if (!$params)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    return [
      'sqlTemplates' => ['LIMIT ', ''],
      'params' => $params,
    ];
  }

  protected function getTemplateOffset() {
    $params = $this->clausules['OFFSET'][0]['params'];

    if (!$params)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    return [
      'sqlTemplates' => ['OFFSET ', ''],
      'params' => $params,
    ];
  }
}

$sqlBuilder = SQL::select('name', 'id')
  ->from('"user"', 'us')
  ->where(
    SQL::eq('name', 'Dan'),
    SQL::sqlNot(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn('id', SQL::select('id')->from('"user"')->where(SQL::eq('type', 'ADM')))
    ),
    SQL::sqlAnd(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn('id', SQL::select('id')->from('"user"')->where(SQL::eq('type', 'ADM')))
    ),
    SQL::sqlOr(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn('id', SQL::select('id')->from('"user"')->where(SQL::eq('type', 'ADM')))
    )
  )
  ->orderBy('id', 'name DESC')
  ->limit(1)
  ->offset(2)
  ->select('login');
