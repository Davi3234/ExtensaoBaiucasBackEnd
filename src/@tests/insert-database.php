<?php

declare(strict_types=1);

require_once __DIR__ . '/../Util/index.php';
require_once __DIR__ . '/../env.php';

use App\Provider\Database\Database;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SQLFormat;

$db = Database::newConnection();

$insertBuilder = new InsertSQLBuilder;

$sql = $insertBuilder
  ->insert('"user"')
  ->params('name', 'login')
  ->value(
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
    ['name' => SQLFormat::toString('dan ruan'), 'login' => SQLFormat::toString('dan.ruan@gmail.com')],
  )
  ->toSql();

var_dump($db->exec($sql, $insertBuilder->getParams()));
