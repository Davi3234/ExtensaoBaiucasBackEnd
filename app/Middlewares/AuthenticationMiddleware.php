<?php

namespace App\Middlewares;

use Core\HTTP\Request;
use Core\HTTP\Response;
use Core\Common\Middleware;
use App\Services\AuthService;
use App\Repositories\UserRepository;

class AuthenticationMiddleware extends Middleware {

  function __construct(
    private readonly AuthService $authService = new AuthService(new UserRepository())
  ) {
  }

  #[\Override]
  function perform(Request $request, Response $response): void {
    $token = $request->getHeader('HTTP_AUTHORIZATION');

    $payload = $this->authService->authorization([
      'token' => $token,
    ]);

    $request->setAttribute('userId', $payload->sub);
  }
}
