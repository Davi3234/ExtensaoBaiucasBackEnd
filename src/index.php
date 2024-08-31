<?php

use App\Model\User;
use App\Provider\Database\Database;
use App\Repository\UserRepository;

require_once __DIR__ . '/env.php';
// require_once __DIR__ . '/app.php';

$repo = new UserRepository(Database::newConnection());

$result = $repo->create(
  new User
);
