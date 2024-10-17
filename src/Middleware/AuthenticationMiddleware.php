<?php

namespace App\Middleware;

use App\Core\Components\Middleware;
use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Exception\Http\UnauthorizedException;

class AuthenticationMiddleware extends Middleware {

  #[\Override]
  function perform(Request $request, Response $response): void {
    // throw new UnauthorizedException('Usuário não autenticado');
  }
}
