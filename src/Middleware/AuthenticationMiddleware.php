<?php

namespace App\Middleware;

use App\Core\Components\Middleware;
use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Repository\UserRepository;
use App\Service\AuthService;

class AuthenticationMiddleware extends Middleware {

  function __construct(
    private readonly AuthService $authService = new AuthService(new UserRepository())
  ) {
  }

  #[\Override]
  function perform(Request $request, Response $response): void {
    $payload = $this->authService->authorization([
      'token' => $request->getHeader('HTTP_AUTHORIZATION'),
    ]);

    $request->setAttribute('userId', $payload->sub);
  }
}
