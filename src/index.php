<?php

$routers = require 'routers.php';

use App\Core\Server;

Server::bootstrap($routers);
