<?php

namespace App\Controllers;

use Core\HTTP\Request;
use App\Services\UserService;
use App\Repositories\UserRepository;

class UserController {
  private readonly UserService $userService;

  function __construct() {
    $this->userService = new UserService(
      new UserRepository()
    );
  }

  function query() {
    $result = $this->userService->query();

    return $result;
  }

  function getOne(Request $request) {
    $result = $this->userService->getById([
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

  function update(Request $request) {
    $result = $this->userService->update([
      'id' => $request->getParam('id'),
      'name' => $request->getBody('name'),
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
