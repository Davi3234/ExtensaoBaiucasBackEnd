<?php

namespace App\Provider\Sql;

class SelectSQLBuilder extends SQLConditionBuilder {

  function __construct() {
    parent::__construct();

    $this->clauses['SELECT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clauses['FROM'] = [];
    $this->clauses['JOIN'] = [];
    $this->clauses['ORDERBY'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clauses['LIMIT'] = [['sqlTemplates' => [], 'params' => []]];
    $this->clauses['OFFSET'] = [['sqlTemplates' => [], 'params' => []]];

    $this->clausesOrder = [
      'WITH' => 'getTemplateWith',
      'SELECT' => 'getTemplateSelect',
      'FROM' => 'getTemplateFrom',
      'JOIN' => 'getTemplateJoin',
      'WHERE' => 'getTemplateWhere',
      'ORDERBY' => 'getTemplateOrderBy',
      'LIMIT' => 'getTemplateLimit',
      'OFFSET' => 'getTemplateOffset',
    ];
  }

  function select(string ...$fields) {
    $this->clauses['SELECT'][0]['sqlTemplates'] = array_merge($this->clauses['SELECT'][0]['sqlTemplates'], $fields);

    return $this;
  }

  function from(string|SelectSQLBuilder $table, string $alias = '') {
    $this->clauses['FROM'][0] = [
      'sqlTemplates' => [$table, $alias],
      'params' => [],
    ];

    return $this;
  }

  function join(SelectSQLBuilder|string $joinTable, string $alias, string $onRelation) {
    return $this->createJoin('JOIN', $joinTable, $alias, $onRelation);
  }

  function innerJoin(SelectSQLBuilder|string $joinTable, string $alias, string $onRelation) {
    return $this->createJoin('INNER JOIN', $joinTable, $alias, $onRelation);
  }

  function leftJoin(SelectSQLBuilder|string $joinTable, string $alias, string $onRelation) {
    return $this->createJoin('LEFT JOIN', $joinTable, $alias, $onRelation);
  }

  function rightJoin(SelectSQLBuilder|string $joinTable, string $alias, string $onRelation) {
    return $this->createJoin('RIGHT JOIN', $joinTable, $alias, $onRelation);
  }

  function fullJoin(SelectSQLBuilder|string $joinTable, string $alias, string $onRelation) {
    return $this->createJoin('FULL JOIN', $joinTable, $alias, $onRelation);
  }

  private function createJoin(string $type, SelectSQLBuilder|string $joinTable, string $alias, string $onRelation) {
    $this->clauses['JOIN'][] = [
      'sqlTemplates' => [$type, $joinTable, $alias, $onRelation],
      'params' => [],
    ];

    return $this;
  }

  /**
   * @param (string|int)[] $values
   */
  function orderBy(array $values = []) {
    $sqlTemplate = $this->clauses['ORDERBY'][0]['sqlTemplates'];

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

    $this->clauses['ORDERBY'][0]['sqlTemplates'] = $sqlTemplate;
    $this->clauses['ORDERBY'][0]['params'] = array_merge($this->clauses['ORDERBY'][0]['params'], $values);

    return $this;
  }

  function limit(string|int $value) {
    $this->clauses['LIMIT'][0] = ['sqlTemplates' => [''], 'params' => [$value]];

    return $this;
  }

  function offset(string|int $value) {
    $this->clauses['OFFSET'][0] = ['sqlTemplates' => [''], 'params' => [$value]];

    return $this;
  }

  protected function getTemplateSelect() {
    $sqlTemplates = $this->clauses['SELECT'][0]['sqlTemplates'];
    $params = $this->clauses['SELECT'][0]['params'];

    if (!$sqlTemplates)
      $sqlTemplates = ['*'];

    return [
      'sqlTemplates' => ['SELECT ' . implode(', ', $sqlTemplates)],
      'params' => $params,
    ];
  }

  protected function getTemplateJoin() {
    $sqlJoins = $this->clauses['JOIN'];

    if (!$sqlJoins)
      return [
        'sqlTemplates' => [],
        'params' => [],
      ];

    $sqlTemplates = [];
    $params = [];

    foreach ($sqlJoins as $sqlJoin) {
      [$type, $joinTable, $alias, $onRelation] = $sqlJoin['sqlTemplates'];

      if ($joinTable instanceof SelectSQLBuilder) {
        $joinTable = $joinTable->getAllTemplatesWithParentheses();

        $params = array_merge($params, $joinTable['params']);

        $joinTable = $joinTable['sqlTemplates'];
      } else {
        $joinTable = [$joinTable];
      }

      $sqlTemplates = self::merge_templates(' ', $sqlTemplates, [$type], $joinTable, ["AS $alias ON $onRelation"]);
    }

    return [
      'sqlTemplates' => $sqlTemplates,
      'params' => $params,
    ];
  }

  protected function getTemplateFrom() {
    $sqlTemplates = [];
    $params = $this->clauses['FROM'][0]['params'];

    foreach ($this->clauses['FROM'][0]['sqlTemplates'] as $template) {
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
    $sqlTemplates = $this->clauses['ORDERBY'][0]['sqlTemplates'];
    $params = $this->clauses['ORDERBY'][0]['params'];

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
    $params = $this->clauses['LIMIT'][0]['params'];

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
    $params = $this->clauses['OFFSET'][0]['params'];

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

$sqlBuilder = SQL::with(
  'user_with',
  SQL::select()
    ->from('"user"')
    ->where([SQL::eq('name', 'dan')])
)
  ->with(
    'user_with2',
    SQL::select()
      ->from('"user"')
      ->where([SQL::eq('name', 'dan')])
  )
  ->select('name', 'id')
  ->from('"user"', 'us')
  ->join('perfil', 'pr', 'pr.id_user = us.id_user')
  ->leftJoin(
    SQL::select()->from('perfil')->where([SQL::eq('type', 'ADM')]),
    'pr1',
    'pr1.id_user = us.id_user'
  )
  ->where([
    SQL::eq('name', 'Dan'),
    SQL::sqlNot(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn(
        'id',
        [
          SQL::select('id')
            ->from('"user"')
            ->where([SQL::eq('type', 'ADM')])
        ]
      )
    ),
    SQL::sqlAnd(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn(
        'id',
        [
          SQL::select('id')
            ->from('"user"')
            ->where([SQL::eq('type', 'ADM')])
        ]
      )
    ),
    SQL::sqlOr(
      SQL::eq('login', 'dan'),
      SQL::eq('active', true),
      SQL::notIn(
        'id',
        [
          SQL::select('id')
            ->from('"user"')
            ->where([SQL::eq('type', 'ADM')])
        ]
      )
    )
  ])
  ->orderBy(['id', 'name DESC'])
  ->limit(1)
  ->offset(2)
  ->select('login');
