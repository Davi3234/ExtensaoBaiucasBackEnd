<?php

namespace App\Service;

use App\Exception\Exception;
use App\Exception\Http\BadRequestException;
use App\Exception\Http\UnauthorizedException;
use App\Model\User;
use App\Repository\IUserRepository;
use App\Provider\Zod\Z;
use App\Provider\JWT;
use PHPUnit\Framework\Attributes\CoversClass;

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
      throw new BadRequestException('Login ou senha inválido');
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

  function authorization(array $args) {
    $token = $args['token'] ?? null;

    if (!$token) {
      throw new UnauthorizedException('Inautorizado', ['causes' => 'Token não definido']);
    }

    if (count(explode(' ', $token)) != 2) {
      throw new UnauthorizedException('Inautorizado', ['causes' => 'Token inválido']);
    }

    [$bearer, $token] = explode(' ', $token);

    if ($bearer !== 'Bearer') {
      throw new UnauthorizedException('Inautorizado', ['causes' => 'Token inválido']);
    }

    try {
      $payload = JWT::decode($token, ['key' => env('JWT_KEY_SECRET')]);

      return $payload;
    } catch (Exception $err) {
      throw new UnauthorizedException('Inautorizado', ['causes' => 'Token inválido']);
    }
  }
}
