<?php

require_once __DIR__ . '/routers.php';

use App\Core\App;

if (!isset($_GET['url']))
  $_GET['url'] = $_SERVER['REQUEST_URI'];

$app = App::CreateApp();
$app->Run();
