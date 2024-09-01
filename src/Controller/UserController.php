<?php

namespace App\Controller;

use App\Core\Components\Request;
use App\Service\UserService;

class UserController {
  private UserService $userService;

  function __construct() {
    $this->userService = new UserService;
  }

  function query() {
    $result = $this->userService->query();
    return $result;
  }

  function getOne(Request $request) {
    $result = $this->userService->getOne([
      'id' => $request->getParam('id'),
    ]);
    return $result;
  }

  function create(Request $request) {
    $result = $this->userService->create([
      'name' => $request->getBody('name'),
      'login' => $request->getBody('login'),
    ]);
    return $result;
  }

  function delete(Request $request) {
    $result = $this->userService->delete([
      'id' => $request->getParam('id'),
    ]);
    return $result;
  }
}
