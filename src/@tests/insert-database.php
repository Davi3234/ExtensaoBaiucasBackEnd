<?php

declare(strict_types=1);

require_once __DIR__ . '/../Util/index.php';
require_once __DIR__ . '/../env.php';

use App\Provider\Database\Database;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SQLFormat;

$db = Database::getGlobalConnection();

$insertBuilder = new InsertSQLBuilder;

$sql = $insertBuilder
  ->insertInto('"user"')
  ->params('name', 'login')
  ->values(
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
  )
  ->build();

var_dump($db->exec($sql['sql'], $sql['params']));
