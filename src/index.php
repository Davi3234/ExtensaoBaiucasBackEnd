<?php
require '..\vendor\autoload.php';

declare(strict_types=1);

use App\Core\App;

$uri = '/users/create';
$method = 'POST';

App::Bootstrap([
  'REQUEST_URI' => $uri,
  'REQUEST_METHOD' => $method,
]);
