<?php

namespace App\Middleware;

use App\Core\Components\Middleware;
use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Exception\UnauthorizedException;

class AuthenticationMiddleware extends Middleware {

  function perform(Request $request, Response $response) {
    // throw new UnauthorizedException('Usu�rio n�o autenticado');
  }
}
