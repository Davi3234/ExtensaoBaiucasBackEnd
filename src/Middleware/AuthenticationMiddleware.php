<?php

namespace App\Middleware;

use App\Core\Components\Middleware;
use App\Core\Components\Request;
use App\Core\Components\Response;

class AuthenticationMiddleware extends Middleware {

  function perform(Request $request, Response $response) {
    echo 11;
  }
}