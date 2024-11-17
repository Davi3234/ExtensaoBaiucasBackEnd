<?php

namespace App\Controllers;

use Core\Common\Attributes\{Controller, Get, Delete, Guard, Post, Put};
use Core\Enum\StatusCodeHTTP;
use Core\HTTP\Request;
use App\Middlewares\AuthenticationMiddleware;
use App\Repositories\UserRepository;
use App\Services\UserService;

#[Controller('/users')]
class UserController
{
  private readonly UserService $userService;

  function __construct()
  {
    $this->userService = new UserService(
      new UserRepository()
    );
  }

  #[Get('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function getOne(Request $request)
  {
    $userId = str_replace("/users/", "", $request->getParam('url'));

    $result = $this->userService->getById([
      'id' => $userId,
    ]);

    return $result;
  }

  #[Get('')]
  #[Guard(AuthenticationMiddleware::class)]
  function getMany(Request $request)
  {
    $result = $this->userService->query();

    return $result;
  }

  #[Post('/', StatusCodeHTTP::CREATED->value)]
  function create(Request $request)
  {
    $result = $this->userService->create([
      'name' => $request->getBody('name'),
      'login' => $request->getBody('login'),
      'password' => $request->getBody('password'),
      'confirm_password' => $request->getBody('confirm_password'),
      'tipo' => $request->getBody('tipo')
    ]);

    return $result;
  }

  #[Put('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function update(Request $request)
  {
    $id = $request->getParam('id');

    $result = $this->userService->update([
      'id' => $id,
      'name' => $request->getBody('name'),
      'login' => $request->getBody('login'),
      'tipo' => $request->getBody('tipo'),
      'password' => $request->getBody('password'),
      'confirm_password' => $request->getBody('confirm_password'),
      'active' => $request->getBody('active')
    ]);

    return $result;
  }

  #[Delete('/:id')]
  #[Guard(AuthenticationMiddleware::class)]
  function delete(Request $request)
  {
    $userId = str_replace("/users/", "", $request->getParam('url'));

    $result = $this->userService->delete([
      'id' => $userId,
    ]);

    return $result;
  }
}
