<?php

namespace App\Controller;

use App\Core\Components\Attribute\Controller;
use App\Core\Components\Attribute\Get;
use App\Core\Components\Request;
use App\Repository\UserRepository;
use App\Service\AuthService;

#[Controller('/auth')]
class AuthController {
  private readonly AuthService $userService;

  function __construct() {
    $this->userService = new AuthService(
      new UserRepository()
    );
  }

  #[Get('/login')]
  function login(Request $request) {
    $result = $this->userService->login([
      'login' => $request->getBody('login'),
      'password' => $request->getBody('password'),
    ]);

    return $result;
  }
}
