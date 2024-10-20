<?php

namespace App\Service;

use App\Exception\Http\BadRequestException;
use App\Provider\Zod\Z;
use App\Repository\IUserRepository;
use Firebase\JWT\JWT;

class AuthService {

  function __construct(
    private readonly IUserRepository $userRepository
  ) {
  }

  function login(array $args) {
    $loginSchema = Z::object([
      'login' => Z::string(),
      'password' => Z::string(),
    ])->coerce();

    $dto = $loginSchema->parseNoSafe($args);

    $user = $this->userRepository->findByLogin($dto->login);

    if (!$user) {
      throw new BadRequestException('Login or password invalid');
    }

    $payload = [
      'exp' => time() + 10,
      'iat' => time(),
      'sub' => $user->getId(),
      'name' => $user->getName(),
    ];

    $token = JWT::encode($payload, env('APP_KEY'), 'HS256');

    return ['message' => 'Teste', 'payload' => $payload, 'token' => $token];
  }
}
