<?php

class SqlBuilderException extends \Exception {
}


class SQL {

    static function eq(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field = "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();

            if (isset($templates['sqlTemplates'][0])) {
                $templates['sqlTemplates'][0] = "({$templates['sqlTemplates'][0]}";
            }

            if (end($templates['sqlTemplates']) !== false) {
                $lastTemplate = end($templates['sqlTemplates']);
                $templates['sqlTemplates'][key($templates['sqlTemplates'])] = "$lastTemplate)";
            }

            var_dump($templates['sqlTemplates'], $templates['params']);

            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else {
            $params[] = $value;
            $sqlTemplates[] = '';
        }

        var_dump($sqlTemplates, $params);

        return static::condition($sqlTemplates, $params);
    }

    static function dif(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field <> "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function gt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field > "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function gte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field >= "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function lt(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field < "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function lte(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field <= "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function like(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field LIKE "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function ilike(string $field, string|int|float|bool|SelectSQLBuilder $value) {
        $sqlTemplates = ["$field ILIKE "];
        $params = [];

        if ($value instanceof SelectSQLBuilder) {
            $templates = $value->fetchAllSqlTemplates();
            $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
            $params = $templates['params'];
        }
        else
            $params[] = $value;

        $sqlTemplates[] = '';

        return static::condition($sqlTemplates, $params);
    }

    static function between(string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
        $sqlTemplates = ["$field BETWEEN "];
        $params = [];
        $values = [$valueLess, $valueGreater];

        foreach($values as $key => $value) {
            if ($value instanceof SelectSQLBuilder) {
                $templates = $value->fetchAllSqlTemplates();
                $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
                $params = array_merge($params, $templates['params']);
            }
            else
                $params = [$valueLess, $valueGreater];

            if ($key == 0)
                $sqlTemplates[] = ' AND ';
            else
                $sqlTemplates[] = '';
        }

        return static::condition($sqlTemplates, $params);
    }

    static function notBetween(string $field, string|int|float|SelectSQLBuilder $valueLess, string|int|float|SelectSQLBuilder $valueGreater) {
        $sqlTemplates = ["$field NOT BETWEEN "];
        $params = [];
        $values = [$valueLess, $valueGreater];

        foreach($values as $key => $value) {
            if ($value instanceof SelectSQLBuilder) {
                $templates = $value->fetchAllSqlTemplates();
                $sqlTemplates = array_merge($sqlTemplates, $templates['sqlTemplates']);
                $params = array_merge($params, $templates['params']);
            }
            else
                $params = [$valueLess, $valueGreater];

            if ($key == 0)
                $sqlTemplates[] = ' AND ';
            else
                $sqlTemplates[] = '';
        }

        return static::condition($sqlTemplates, $params);
    }

    static function in(string $field, string|int|float|SelectSQLBuilder $value, string|int|float ...$values) {
        $sqlTemplates = ["$field IN ("];
        $params = [];

        if ($value instanceof SelectSQLBuilder)
            $values = [];

        array_unshift($values, $value);

        foreach($values as $value) {
          $sqlTemplates[] = ', ';
        }

        $sqlTemplates[] = ')';

        return static::condition($sqlTemplates, $values);
    }

    static function notIn($field, $value, ...$values) {
        array_unshift($values, $value);

        $sqlTemplates = ["$field NOT IN ("];
        foreach($values as $value) {
          $sqlTemplates[] = ', ';
        }
        $sqlTemplates[] = ')';

        return static::condition($sqlTemplates, $values);
    }

    static function isNull($field) {
        return static::condition(["$field IS NULL"]);
    }

    static function isNotNull($field) {
        return static::condition(["$field IS NOT NULL"]);
    }

    static function isTrue($field) {
        return static::condition(["$field IS TRUE"]);
    }

    static function isFalse($field) {
        return static::condition(["$field IS FALSE"]);
    }

    static function isDistinctFrom($field, $value) {
        return static::condition(["$field IS DISTINCT FROM $value"]);
    }

    static function exists($subSelect) {
        return static::condition(["EXISTS ($subSelect)"]);
    }

    static function notExists($subSelect) {
        return static::condition(["NOT EXISTS ($subSelect)"]);
    }

    static function similarTo($field, $value) {
        return static::condition(["$field SIMILAR TO $value"]);
    }

    static function notSimilarTo($field, $value) {
        return static::condition(["$field NOT SIMILAR TO $value"]);
    }

    static function sqlAnd(...$conditions) {
        return static::logical('AND', $conditions);
    }

    static function sqlOr(...$conditions) {
        return static::logical('OR', $conditions);
    }

    static function not(...$conditions) {
        return static::logical('NOT', $conditions);
    }

    /**
     * @param (string|SelectSQLBuilder)[] $sqlTemplates
     * @return array{sqlTemplates: string|SelectSQLBuilder[], params: (string|number|boolean|null)[]}
     */
    private static function condition(array $sqlTemplates, array $params = []) {
        if (count($params) != count($sqlTemplates) - 1)
            throw new SqlBuilderException("TODO");

        return [
            'sqlTemplates' => $sqlTemplates,
            'params' => $params
        ];
    }

    private static function logical(string $type, array $nested) {
        return [
            'type' => $type,
            'nested' => $nested
        ];
    }

    static function orderBy(...$orderByArgs) {
        $sql = '';

        if (count($orderByArgs) > 0)
            $sql = "ORDER BY " . implode(', ', $orderByArgs);

        return [
            'sql' => $sql,
            'clausule' => 'ORDERBY'
        ];
    }

    static function groupBy(...$groupByArgs) {
        $sql = '';

        if (count($groupByArgs) > 0)
            $sql = "GROUP BY " . implode(', ', $groupByArgs);

        return [
            'sql' => $sql,
            'clausule' => 'GROUPBY'
        ];
    }

    static function limit($limitArgs) {
        $sql = '';

        if (trim($limitArgs))
            $sql = "LIMIT $limitArgs";

        return [
            'sql' => $sql,
            'clausule' => 'LIMIT'
        ];
    }

    static function offset($offsetArgs) {
        $sql = '';

        if (trim($offsetArgs))
            $sql = "OFFSET $offsetArgs";

        return [
            'sql' => $sql,
            'clausule' => 'OFFSET'
        ];
    }
}


abstract class SQLBuilder {

    function __construct() {}

      /**
       * @var array<string,array{sqlTemplates: string|SelectSQLBuilder[], params: (string|number|boolean|null)[]}[]>
       */
      protected array $clausules = [];
      protected $params = [];

      function build() {
          return '';
      }

      /**
       * @return array{sqlTemplates: string[], params: (string|number|boolean|null)[]}
       */
      abstract function fetchAllSqlTemplates(): array;

      function createParam($value) {
          $this->params[] = $value;

          $number = count($this->params);

          return "$$number";
      }

      function getParams() {
          return $this->params;
      }
  }

class SelectSQLBuilder extends SQLBuilder {

    function fetchAllSqlTemplates(): array {
      return [
        'sqlTemplates' => ['SELECT * FROM user WHERE login = ', ''],
        'params' => ['Dan'],
      ];
    }
  }

printSQL(
    SQL::eq('name', 'Dan')
);

printSQL(
    SQL::dif('name', 'Dan')
);

printSQL(
    SQL::gt('name', 'Dan')
);

printSQL(
    SQL::gte('name', 'Dan')
);

printSQL(
    SQL::lt('name', 'Dan')
);

printSQL(
    SQL::lte('name', 'Dan')
);

printSQL(
    SQL::eq('name', new SelectSQLBuilder)
);

function printSQL($sql) {
    ?>
    <script>
        console.log(<?= json_encode($sql['sqlTemplates']) ?>, <?= json_encode($sql['params']) ?>)
    </script>
    <?php
}