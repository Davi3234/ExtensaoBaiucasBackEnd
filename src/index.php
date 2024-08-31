<?php

use App\Model\User;
use App\Provider\Database\Database;
use App\Provider\Sql\SQL;
use App\Repository\UserRepository;

require_once __DIR__ . '/env.php';
// require_once __DIR__ . '/app.php';

$repo = new UserRepository(Database::newConnection());

$result = $repo->findMany(SQL::select());

var_dump($result);
