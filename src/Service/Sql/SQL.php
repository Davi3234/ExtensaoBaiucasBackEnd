<?php

namespace App\Service\Sql;

class SQL {

    /**
     * Clausule WITH
     * @param string $alias Alias name for With statement
     * @param string $select Pre-query for With statement
     * @throws \Exception Alias not defined for clause "WITH"
     * @throws \Exception Select not defined for clause "WITH"
     * @return array{sql: string, clausule: string} SQL Statement for With clausule
     */
    static function with($alias, $select) {
        if (!trim($alias))
            throw new \Exception('Alias not defined for clause "WITH"');

        if (!trim($select))
            throw new \Exception('Select not defined for clause "WITH"');

        return [
            'sql' => "WITH $alias ($select)",
            'clausule' => 'WITH'
        ];
    }

    /**
     * Clausule SELECT
     * @param mixed ...$fields Fields of query
     * @return array{sql: string, clausule: string} SQL Statement for Select clausule
     */
    static function select(...$fields) {
        $sql = implode(', ', $fields);

        if (count($fields) == 0)
            $sql = '*';

        return [
            'sql' => "SELECT $sql",
            'clausule' => 'SELECT'
        ];
    }

    /**
     * Clausule FROM
     * @param string $table Table name
     * @param ?string $alias Alias name
     * @throws \Exception Table name not defined for clause "FROM"
     * @return array{sql: string, clausule: string} SQL Statement for From clausule
     */
    static function from($table, $alias = '') {
        if (!trim($table))
            throw new \Exception('Table name not defined for clause "FROM"');

        $sql = trim("$table $alias");

        return [
            'sql' => "FROM $sql",
            'clausule' => 'FROM'
        ];
    }

    /**
     * Clausule JOIN
     * @param string $table Table name
     * @param string $alias Alias name
     * @param string $on Condition of the relation table
     * @throws \Exception Table name not defined for clause "{clausule}"
     * @throws \Exception Alias not defined for clause "{clausule} table "{table}"
     * @throws \Exception On not defined for clause "{clausule} table "{table}" "{alias}"
     * @return array{sql: string, clausule: string} SQL Statement for Join clausule
     */
    static function join($table, $alias, $on) {
        return static::createJoin('', $table, $alias, $on);
    }

    /**
     * Clausule LEFT JOIN
     * @param string $table Table name
     * @param string $alias Alias name
     * @param string $on Condition of the relation table
     * @throws \Exception Table name not defined for clause "{clausule}"
     * @throws \Exception Alias not defined for clause "{clausule} table "{table}"
     * @throws \Exception On not defined for clause "{clausule} table "{table}" "{alias}"
     * @return array{sql: string, clausule: string} SQL Statement for Left Join clausule
     */
    static function leftJoin($table, $alias, $on) {
        return static::createJoin('LEFT', $table, $alias, $on);
    }

    /**
     * Clausule RIGHT JOIN
     * @param string $table Table name
     * @param string $alias Alias name
     * @param string $on Condition of the relation table
     * @throws \Exception Table name not defined for clause "{clausule}"
     * @throws \Exception Alias not defined for clause "{clausule} table "{table}"
     * @throws \Exception On not defined for clause "{clausule} table "{table}" "{alias}"
     * @return array{sql: string, clausule: string} SQL Statement for Right Join clausule
     */
    static function rightJoin($table, $alias, $on) {
        return static::createJoin('RIGHT', $table, $alias, $on);
    }

    /**
     * Clausule INNER JOIN
     * @param string $table Table name
     * @param string $alias Alias name
     * @param string $on Condition of the relation table
     * @throws \Exception Table name not defined for clause "{clausule}"
     * @throws \Exception Alias not defined for clause "{clausule} table "{table}"
     * @throws \Exception On not defined for clause "{clausule} table "{table}" "{alias}"
     * @return array{sql: string, clausule: string} SQL Statement for Inner Join clausule
     */
    static function innerJoin($table, $alias, $on) {
        return static::createJoin('INNER', $table, $alias, $on);
    }

    /**
     * Clausule FULL JOIN
     * @param string $table Table name
     * @param string $alias Alias name
     * @param string $on Condition of the relation table
     * @throws \Exception Table name not defined for clause "{clausule}"
     * @throws \Exception Alias not defined for clause "{clausule} table "{table}"
     * @throws \Exception On not defined for clause "{clausule} table "{table}" "{alias}"
     * @return array{sql: string, clausule: string} SQL Statement for Full Join clausule
     */
    static function fullJoin($table, $alias, $on) {
        return static::createJoin('FULL', $table, $alias, $on);
    }

    /**
     * Clausule JOIN
     * @param string $prefix Type Join (LEFT, RIGHT, INNER, FULL, or empty when type is only JOIN)
     * @param string $table Table name
     * @param string $alias Alias name
     * @param string $on Condition of the relation table
     * @throws \Exception Table name not defined for clause "{clausule}"
     * @throws \Exception Alias not defined for clause "{clausule} table "{table}"
     * @throws \Exception On not defined for clause "{clausule} table "{table}" "{alias}"
     * @return array{sql: string, clausule: string} SQL Statement for Join clausule
     */
    private static function createJoin($prefix, $table, $alias, $on) {
        $clausule = trim("$prefix JOIN");

        if (!trim($table))
            throw new \Exception("Table name not defined for clause \"$clausule\"");

        if (!trim($alias))
            throw new \Exception("Alias not defined for clause \"$clausule\" table \"$table\"");

        if (!trim($on))
            throw new \Exception("On not defined for clause \"$clausule\" table \"$table\" \"$alias\"");

        $sql = trim("$table $alias ON $on");

        return [
            'sql' => trim("$clausule $sql"),
            'clausule' => 'JOIN'
        ];
    }

    /**
     * Clausule ORDER BY
     * @param string|numeric ...$orderByArgs Arguments of the sort
     * @return array{sql: string, clausule: string} SQL Statement for Order By clausule
     */
    static function orderBy(...$orderByArgs) {
        $sql = '';

        if (count($orderByArgs) > 0)
            $sql = "ORDER BY " . implode(', ', $orderByArgs);

        return [
            'sql' => $sql,
            'clausule' => 'ORDERBY'
        ];
    }

    /**
     * Clausule GROUP BY
     * @param string|numeric ...$groupByArgs Arguments of the group
     * @return array{sql: string, clausule: string} SQL Statement for Group By clausule
     */
    static function groupBy(...$groupByArgs) {
        $sql = '';

        if (count($groupByArgs) > 0)
            $sql = "GROUP BY " . implode(', ', $groupByArgs);

        return [
            'sql' => $sql,
            'clausule' => 'GROUPBY'
        ];
    }

    /**
     * Clausule LIMIT
     * @param string|numeric $limitArgs Limit
     * @return array{sql: string, clausule: string} SQL Statement for Limit By clausule
     */
    static function limit($limitArgs) {
        $sql = '';

        if (trim($limitArgs))
            $sql = "LIMIT $limitArgs";

        return [
            'sql' => $sql,
            'clausule' => 'LIMIT'
        ];
    }

    /**
     * Clausule OFFSET
     * @param string|numeric $offsetArgs Offset
     * @return array{sql: string, clausule: string} SQL Statement for Offset clausule
     */
    static function offset($offsetArgs) {
        $sql = '';

        if (trim($offsetArgs))
            $sql = "OFFSET $offsetArgs";

        return [
            'sql' => $sql,
            'clausule' => 'OFFSET'
        ];
    }

    /**
     * Clausule WHERE
     * Operator equals:
     * ```sql
     * id = 1
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function eq($field, $value) {
        return static::condition("$field = $value");
    }

    /**
     * Clausule WHERE
     * Operator differents:
     * ```sql
     * id <> 1
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function dif($field, $value) {
        return static::condition("$field <> $value");
    }

    /**
     * Clausule WHERE
     * Operator greater than:
     * ```sql
     * id > 10
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function gt($field, $value) {
        return static::condition("$field > $value");
    }

    /**
     * Clausule WHERE
     * Operator greater or equal than:
     * ```sql
     * id >= 10
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function gte($field, $value) {
        return static::condition("$field >= $value");
    }

    /**
     * Clausule WHERE
     * Operator less or equal than:
     * ```sql
     * id < 10
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function lt($field, $value) {
        return static::condition("$field < $value");
    }

    /**
     * Clausule WHERE
     * Operator less or equal than:
     * ```sql
     * id <= 10
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function lte($field, $value) {
        return static::condition("$field <= $value");
    }

    /**
     * Clausule WHERE
     * Operator like:
     * ```sql
     * name LIKE '%Will%'
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function like($field, $value) {
        return static::condition("$field LIKE $value");
    }

    /**
     * Clausule WHERE
     * Operator ilike:
     * ```sql
     * name ILIKE '%will%'
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function ilike($field, $value) {
        return static::condition("$field ILIKE $value");
    }

    /**
     * Clausule WHERE
     * Operator between:
     * ```sql
     * name BETWEEN '2024-01-01' AND '2024-01-31'
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $valueLess Value less to be compared
     * @param string|numeric $valueGreater Value greater to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function between($field, $valueLess, $valueGreater) {
        return static::condition("$field BETWEEN $valueLess AND $valueGreater");
    }

    /**
     * Clausule WHERE
     * Operator between:
     * ```sql
     * name NOT BETWEEN '2024-01-01' AND '2024-01-31'
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $valueLess Value less to be compared
     * @param string|numeric $valueGreater Value greater to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function notBetween($field, $valueLess, $valueGreater) {
        return static::condition("$field NOT BETWEEN $valueLess AND $valueGreater");
    }

    /**
     * Clausule WHERE
     * Operator in:
     * ```sql
     * id IN (1, 2, 3, 4, 5, 6)
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric ...$values Values to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function in($field, ...$values) {
        return static::condition("$field IN (" . implode(', ', $values) . ")");
    }

    /**
     * Clausule WHERE
     * Operator not in:
     * ```sql
     * id NOT IN (1, 2, 3, 4, 5, 6)
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric ...$values Values to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function notIn($field, ...$values) {
        return static::condition("$field NOT IN (" . implode(', ', $values) . ")");
    }

    /**
     * Clausule WHERE
     * Operator is null:
     * ```sql
     * description IS NULL
     * ```
     * @param string|numeric $field Argument to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function isNull($field) {
        return static::condition("$field IS NULL");
    }

    /**
     * Clausule WHERE
     * Operator is not null:
     * ```sql
     * description IS NOT NULL
     * ```
     * @param string|numeric $field Argument to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function isNotNull($field) {
        return static::condition("$field IS NOT NULL");
    }

    /**
     * Clausule WHERE
     * Operator is true:
     * ```sql
     * active IS TRUE
     * ```
     * @param string|numeric $field Argument to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function isTrue($field) {
        return static::condition("$field IS TRUE");
    }

    /**
     * Clausule WHERE
     * Operator is false:
     * ```sql
     * active IS FALSE
     * ```
     * @param string|numeric $field Argument to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function isFalse($field) {
        return static::condition("$field IS FALSE");
    }

    /**
     * Clausule WHERE
     * Operator is distinct from:
     * ```sql
     * active IS DISTINCT FROM false
     * ```
     * @param string|numeric $field Argument to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function isDistinctFrom($field, $value) {
        return static::condition("$field IS DISTINCT FROM $value");
    }

    /**
     * Clausule WHERE
     * Operator exists:
     * ```sql
     * EXISTS (SELECT * FROM user WHERE id = 1)
     * ```
     * @param string|SelectSQLBuilder $subSelect Sub-select to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function exists($subSelect) {
        if ($subSelect instanceof SelectSQLBuilder)
            $subSelect = $subSelect->toSql();

        return static::condition("EXISTS ($subSelect)");
    }

    /**
     * Clausule WHERE
     * Operator not exists:
     * ```sql
     * NOT EXISTS (SELECT * FROM user WHERE id = 1)
     * ```
     * @param string|SelectSQLBuilder $subSelect Sub-select to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function notExists($subSelect) {
        if ($subSelect instanceof SelectSQLBuilder)
            $subSelect = $subSelect->toSql();

        return static::condition("NOT EXISTS ($subSelect)");
    }

    /**
     * Clausule WHERE
     * Operator similar to:
     * ```sql
     * 'abc' SIMILAR TO '%(b|d)%'
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function similarTo($field, $value) {
        return static::condition("$field SIMILAR TO $value");
    }

    /**
     * Clausule WHERE
     * Operator similar to:
     * ```sql
     * 'abc' NOT SIMILAR TO '(b|c)%'
     * ```
     * @param string|numeric $field Argument left of the operation
     * @param string|numeric $value Value to be compared
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function notSimilarTo($field, $value) {
        return static::condition("$field NOT SIMILAR TO $value");
    }

    /**
     * Clausule WHERE
     * Operator logical and:
     * ```sql
     * 1 = 1 AND 2 > 1
     * ```
     * @param array{sql: string}[] ...$conditions Conditions
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function sqlAnd(...$conditions) {
        return static::logical('AND', $conditions);
    }

    /**
     * Clausule WHERE
     * Operator logical or:
     * ```sql
     * 1 <> 1 OR 2 > 1
     * ```
     * @param array{sql: string}[] ...$conditions Conditions
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function sqlOr(...$conditions) {
        return static::logical('OR', $conditions);
    }

    /**
     * Clausule WHERE
     * Operator logical NOT:
     * ```sql
     * NOT (1 <> 1 AND 2 > 1)
     * ```
     * @param array{sql: string}[] ...$conditions Conditions
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function not(...$conditions) {
        return static::logical('NOT', $conditions);
    }

    /**
     * Clausule WHERE
     * @param string $sql Sql of the condition
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function condition($sql) {
        return [
            'sql' => $sql,
            'clausule' => 'WHERE'
        ];
    }

    /**
     * Clausule WHERE
     * @param string $type Type of the operator logical (AND, OR, NOT)
     * @param array{sql: string} $nested Sql of the condition
     * @return array{sql: string, clausule: string} SQL Statement for Where clausule
     */
    static function logical($type, $nested) {
        return [
            'type' => $type,
            'nested' => $nested,
            'clausule' => 'WHERE'
        ];
    }

    /**
     * Clausule INSERT
     * @param string $table Table name
     * @return array{sql: string, clausule: string} SQL Statement for Insert clausule
     */
    static function insert($table) {
        if (!$table)
            throw new \Exception('Table name not defined for clausule "INSERT"');

        return [
            'sql' => "INSERT INTO $table",
            'clausule' => 'INSERT'
        ];
    }

    /**
     * Clausule UPDATE
     * @param string $table Table name
     * @return array{sql: string, clausule: string} SQL Statement for Update clausule
     */
    static function update($table) {
        if (!$table)
            throw new \Exception('Table name not defined for clausule "UPDATE"');

        return [
            'sql' => "UPDATE $table",
            'clausule' => 'UPDATE'
        ];
    }

    /**
     * Clausule DELETE
     * @param string $table Table name
     * @return array{sql: string, clausule: string} SQL Statement for Delete clausule
     */
    static function delete($table) {
        if (!$table)
            throw new \Exception('Table name not defined for clausule "DELETE"');

        return [
            'sql' => "DELETE FROM $table",
            'clausule' => 'DELETE'
        ];
    }
}
