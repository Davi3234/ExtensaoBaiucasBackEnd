<?php

namespace Tests\User;

use App\Exception\Http\BadRequestException;
use App\Model\User;
use App\Provider\JWT;
use App\Repository\IUserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use App\Service\AuthService;

class AuthTest extends TestCase {

  #[Test]
  public function testDeveEfetuarLogin() {
    // Arrange
    $login = 'dan@gmail.com';
    $password = 'Abc123!@#';

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('findByLogin')
      ->with($login)
      ->willReturn(
        User::__loadModel([
          'id' => 1,
          'name' => 'Dan Ruan',
          'login' => $login,
          'password' => md5('Abc123!@#')
        ])
      );

    // Action
    $authService = new AuthService($userRepository);

    $result = $authService->login([
      'login' => $login,
      'password' => $password,
    ]);

    // Assertion
    $this->assertIsString($result['token']);

    $decoded = JWT::decode($result['token'], ['key' => env('JWT_KEY_SECRET')]);

    $this->assertIsObject($decoded);
    $this->assertEquals($decoded->sub, 1);
    $this->assertEquals($decoded->name, 'Dan Ruan');
  }

  #[Test]
  public function testUsuarioNaoEncontrado() {
    // Arrange
    $login = 'dan@gmail.com';
    $password = 'Abc123!@#';

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('findByLogin')->with($login)->willReturn(null);

    // Assertion
    $this->expectException(BadRequestException::class);

    // Action
    $authService = new AuthService($userRepository);

    $authService->login([
      'login' => $login,
      'password' => $password,
    ]);
  }

  #[Test]
  public function testSenhaInvalida() {
    // Arrange
    $login = 'dan@gmail.com';
    $password = 'Abc123!@#';

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('findByLogin')
      ->with($login)
      ->willReturn(
        User::__loadModel([
          'id' => 1,
          'name' => 'Dan Ruan',
          'login' => $login,
          'password' => md5('Abc123!@#123435gtbs')
        ])
      );

    // Assertion
    $this->expectException(BadRequestException::class);

    // Action
    $authService = new AuthService($userRepository);

    $authService->login([
      'login' => $login,
      'password' => $password,
    ]);
  }
}
