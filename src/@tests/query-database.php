<?php

declare(strict_types=1);

require_once __DIR__ . '/../Util/index.php';
require_once __DIR__ . '/../env.php';

use App\Service\Sql\SelectSQLBuilder;
use App\Service\Sql\SQL;
use App\Service\Sql\SQLFormat;

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
