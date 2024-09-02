<?php

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
