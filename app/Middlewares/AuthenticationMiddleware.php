<?php

namespace App\Middlewares;

use Core\HTTP\Request;
use Core\HTTP\Response;
use Core\Common\Middleware;
use App\Services\AuthService;
use App\Repositories\UserRepository;
use Core\Exception\HTTP\UnauthorizedException;
use Exception\ValidationException;

class AuthenticationMiddleware extends Middleware {

  function __construct(
    private readonly AuthService $authService = new AuthService(new UserRepository())
  ) {
  }

  #[\Override]
  function perform(Request $request, Response $response): void {
    try {
      $token = $request->getHeader('HTTP_AUTHORIZATION');

      $payload = $this->authService->authorization([
        'token' => $token,
      ]);

      $request->setAttribute('userId', $payload->sub);
      $request->setAttribute('userLogin', $payload->login);
      $request->setAttribute('userName', $payload->name);
    } catch (ValidationException $err) {
      throw new UnauthorizedException($err->getMessage(), $err->getCauses());
    }
  }
}
