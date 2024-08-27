<?php

declare(strict_types=1);

require_once __DIR__ . '/../Util/index.php';
require_once __DIR__ . '/../env.php';

use App\Provider\Database\Database;

$db = Database::newConnection();

$transaction = $db->transaction();

$transaction->begin();
$checkpoint = $transaction->save();

$result = $db->exec('INSERT INTO "user" (name, login) VALUES ($1, $2)', ['Dan Ruanaaa', 'danruan']);

var_dump($result);

$checkpoint->rollback();
$transaction->commit();
