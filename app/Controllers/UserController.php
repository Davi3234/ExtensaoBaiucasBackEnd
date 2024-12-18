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

  #[Get('/current')]
  #[Guard(AuthenticationMiddleware::class)]
  function getCurrent(Request $request) {
    $result = $this->userService->getById([
      'id' => $request->getAttribute('userId'),
    ]);

    return $result;
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request) {
    $result = $this->userService->getById([
      'id' => $request->getParam('id'),
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request) {
    $result = $this->userService->query();

    return $result;
  }

  #[Post('/', StatusCodeHTTP::CREATED->value)]
  function create(Request $request) {
    $result = $this->userService->createUser([
      'name' => $request->getBody('name'),
      'login' => $request->getBody('login'),
      'password' => $request->getBody('password'),
      'confirm_password' => $request->getBody('confirmPassword'),
      'tipo' => $request->getBody('tipo'),
      'cpf' => $request->getBody('cpf'),
      'endereco' => $request->getBody('endereco'),
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request) {
    $id = $request->getParam('id');

    $result = $this->userService->update([
      'id' => $id,
      'name' => $request->getBody('name'),
      'login' => $request->getBody('login'),
      'tipo' => $request->getBody('tipo'),
      'password' => $request->getBody('password'),
      'confirm_password' => $request->getBody('confirmPassword'),
      'active' => $request->getBody('active')
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request) {
    $result = $this->userService->delete([
      'id' => $request->getParam('id'),
    ]);

    return $result;
  }
}
