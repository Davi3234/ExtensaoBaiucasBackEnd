<?php

namespace App\Controllers;

use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use Core\Enum\StatusCodeHTTP;
use Core\HTTP\Request;
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\UserRepository;
use App\Services\UserService;

#[Controller('/users')]
class UserController {
  private readonly UserService $userService;

  function __construct() {
    $this->userService = new UserService(
      new UserRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $userId = $request->getAttribute('userId');

    $result = $this->userService->getById([
      'id' => $userId,
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request) {

    $result = $this->userService->query();

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request) {

    $result = $this->userService->query();

    return $result;
  }

  #[Post('/create', StatusCodeHTTP::CREATED->value)]
  function create(Request $request) {
    $result = $this->userService->create([
      'name' => $request->getBody('name'),
      'login' => $request->getBody('login'),
      'password' => $request->getBody('password')
    ]);

    return $result;
  }

  #[Put('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $userId = $request->getAttribute('userId');

    $result = $this->userService->update([
      'id' => $userId,
      'name' => $request->getBody('name'),
    ]);

    return $result;
  }

  #[Delete('/')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $userId = $request->getAttribute('userId');

    $result = $this->userService->delete([
      'id' => $userId,
    ]);

    return $result;
  }
}
