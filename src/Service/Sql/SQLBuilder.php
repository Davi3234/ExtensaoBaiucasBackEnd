<?php

namespace App\Service\Sql;

class SQLBuilder {
    /**
     * Summary of clausules
     * @var array<string,mixed>
     */
    protected $clausules = [];
    protected $params = [];

    /**
     * Method responsible for generating the sql
     * @return string
     */
    function toSql() {
        return '';
    }

    /**
     * Get the clausules statement
     * @return array<string, mixed>
     */
    function getClausules() {
        return $this->clausules;
    }

    /**
     * Get the clausule statement
     * @return mixed
     */
    function getClausule($clausule) {
        return $this->clausules[$clausule];
    }

    function createParam($value) {
        $this->params[] = $value;

        $number = count($this->params);

        return "$$number";
    }

    function getParams() {
        return $this->params;
    }
}
