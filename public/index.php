<?php

require_once __DIR__ . '/../initialize.php';

$routers = require __DIR__ . '/routers.php';

\Core\Server::Bootstrap($routers)->dispatch();
