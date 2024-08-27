<?php

declare(strict_types=1);

require_once __DIR__ . '/../Util/index.php';
require_once __DIR__ . '/../env.php';

use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\SQL;
use App\Provider\Sql\SQLFormat;

$insertBuilder = new SelectSQLBuilder;

$insertBuilder
  ->from('"user"');

$insertBuilder->where(
  SQL::sqlOr(
    SQL::ilike('name', SQLFormat::toString('%Dan'))
  )
);

$sql = $insertBuilder->toSql();

echo $sql;
