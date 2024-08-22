<?php

declare(strict_types=1);

require_once __DIR__ . '/routers.php';

use App\Core\App;

// $_GET['url'] = $_SERVER['REQUEST_URI'];

$app = App::CreateApp();
$app->Run();
