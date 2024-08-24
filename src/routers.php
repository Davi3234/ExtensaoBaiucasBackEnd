<?php

use App\Core\Components\Router;

Router::get('/status', function () {
  return true;
});

Router::writeRouter([
  'prefix' => '/users',
  'filePath' => 'Router/UserRouter.php',
]);
