<?php

namespace App\Controllers;

use Core\HTTP\Request;
use Core\Common\Attributes\Get;
use Core\Common\Attributes\Controller;
use App\Services\AuthService;
use App\Repositories\UserRepository;

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
      'login' => $request->getParam('login'),
      'password' => $request->getParam('password'),
    ]);

    return $result;
  }
}
