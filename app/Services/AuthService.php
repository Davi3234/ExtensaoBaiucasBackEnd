<?php

namespace App\Services;

use PHPUnit\Framework\Attributes\CoversClass;
use Exception\ValidationException;
use Provider\JWT\JWTException;
use Provider\Zod\Z;
use Provider\JWT\JWT;
use App\Models\User;
use App\Repositories\IUserRepository;

#[CoversClass(User::class)]
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
      throw new ValidationException('Login ou senha inválido');
    }

    $payload = [
      'sub' => $user->getId(),
      'login' => $user->getLogin(),
      'name' => $user->getName(),
    ];

    $token = JWT::encode($payload, [
      'key' => env('JWT_KEY_SECRET'),
      'exp' => env('JWT_EXP')
    ]);

    return ['token' => $token];
  }

  function authorization(array $args) {
    $token = $args['token'] ?? null;

    if (!$token) {
      throw new ValidationException('Não Autorizado', ['causes' => 'Token não definido']);
    }

    if (count(explode(' ', $token)) != 2) {
      throw new ValidationException('Não Autorizado', ['causes' => 'Token inválido']);
    }

    [$bearer, $token] = explode(' ', $token);

    if ($bearer !== 'Bearer') {
      throw new ValidationException('Não Autorizado', ['causes' => 'Token inválido']);
    }

    try {
      $payload = JWT::decode($token, ['key' => env('JWT_KEY_SECRET')]);

      return $payload;
    } catch (JWTException $err) {
      throw new ValidationException('Não Autorizado', ['causes' => 'Token inválido']);
    }
  }
}
