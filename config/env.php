<?php

$pathsToEnv = [
  __DIR__ . '/..',
];

\Dotenv\Dotenv::createImmutable($pathsToEnv)->load();
