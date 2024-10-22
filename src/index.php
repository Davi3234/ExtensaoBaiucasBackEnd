<?php

$routers = require 'routers.php';

use App\Core\Server;

Server::Fabric($routers)->Run();
