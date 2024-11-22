<?php

require __DIR__.'/../../initialize.php';

use Core\Managers\RequestManager;

function measurePerformance(callable $handler, int $iterations = 1): array {
  $times = [];
  $totalTime = 0;

  for ($i = 0; $i < $iterations; $i++) {
      $startTime = hrtime(true);
      $handler();
      $endTime = hrtime(true);

      $executionTime = ($endTime - $startTime) / 1e+6;
      $times[] = $executionTime;
      $totalTime += $executionTime;
  }

  return [
      'max_time' => max($times),
      'min_time' => min($times),
      'average_time' => $totalTime / $iterations,
      'total_time' => $totalTime,
  ];
}

$requests = [
  ['/auth/login', 'POST'],
  ['/users', 'POST'],
  ['/orders', 'POST'],
  ['/items/create', 'POST'],
  ['/products', 'POST'],
  ['/categories', 'POST'],
  ['/users/current', 'GET'],
  ['/categories', 'GET'],
  ['/users', 'GET'],
  ['/orders', 'GET'],
  ['/products', 'GET'],
  ['/categories/products', 'GET'],
  ['/users/1', 'GET'],
  ['/orders/1', 'GET'],
  ['/items/1', 'GET'],
  ['/products/1', 'GET'],
  ['/categories/1', 'GET'],
  ['/users/1', 'PUT'],
  ['/orders/1', 'PUT'],
  ['/items/1', 'PUT'],
  ['/products/1', 'PUT'],
  ['/categories/1', 'PUT'],
  ['/users/1', 'DELETE'],
  ['/orders/1', 'DELETE'],
  ['/items/1', 'DELETE'],
  ['/products/1', 'DELETE'],
  ['/categories/1', 'DELETE'],
];

array_map(function($router) {
  $result = measurePerformance(function() use($router) {
    $request = new RequestManager([], $router[0], $router[1]);
  
    $request->loadEndpointFromCacheFile();
  });
  
  echo "Rota: \"$router[0]\" \"$router[1]\"\n";
  echo "Maior tempo executado: {$result['max_time']} ms\n";
  echo "Menor tempo executado: {$result['min_time']} ms\n";
  echo "Média de tempo executado: {$result['average_time']} ms\n";
  echo "Tempo total executado: {$result['total_time']} ms\n".PHP_EOL;
}, $requests);