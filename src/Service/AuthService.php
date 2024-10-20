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

    if ($user->getPassword() != md5($dto->password)) {
      throw new BadRequestException('Login or password invalid');
    }

    $payload = [
      'exp' => time() + env('JWT_EXP'),
      'iat' => time(),
      'sub' => $user->getId(),
      'name' => $user->getName(),
    ];

    $token = JWT::encode($payload, env('JWT_KEY_SECRET'), 'HS256');

    return ['token' => $token];
  }
}
