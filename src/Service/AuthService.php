<?php

namespace App\Service;

use App\Exception\Http\BadRequestException;
use App\Repository\IUserRepository;
use App\Provider\Zod\Z;
use App\Provider\JWT;

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

    if (!$user || $user->getPassword() != md5($dto->password)) {
      throw new BadRequestException('Login or password invalid');
    }

    $payload = [
      'sub' => $user->getId(),
      'name' => $user->getName(),
    ];

    $token = JWT::encode($payload, [
      'key' => env('JWT_KEY_SECRET'),
      'exp' => env('JWT_EXP')
    ]);

    return ['token' => $token];
  }
}
