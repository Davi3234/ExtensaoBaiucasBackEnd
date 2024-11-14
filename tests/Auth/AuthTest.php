<?php

namespace Tests\Auth;

use App\Models\User;
use App\Repositories\IUserRepository;
use App\Services\AuthService;
use Exception\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Provider\JWT\JWT;

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
        new User(
          id: 1,
          name: 'Dan Ruan',
          login: $login,
          password: md5($password),
          active: true
        )
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
    $this->expectException(ValidationException::class);

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
        new User(
          id: 1,
          name: 'Dan Ruan',
          login: $login,
          password: md5('Abc123!@#123435gtbs'),
          active: true
        )
      );

    // Assertion
    $this->expectException(ValidationException::class);

    // Action
    $authService = new AuthService($userRepository);

    $authService->login([
      'login' => $login,
      'password' => $password,
    ]);
  }

  // Authentication
  #[Test]
  public function testDevePermitirAutorizarUsuario() {
    // Arrange
    $token  = $this->tokenFactory();
    $authorization = "Bearer $token";

    // Action
    $userRepository = TestCase::createMock(IUserRepository::class);
    $authService = new AuthService($userRepository);

    $payload = $authService->authorization([
      'token' => $authorization
    ]);

    // Assertion
    $this->assertIsObject($payload);
  }

  #[Test]
  public function testTokenInvalido_EspacoAMais() {
    // Arrange
    $token  = $this->tokenFactory();
    $authorization = "Bearer  $token";

    $this->expectException(ValidationException::class);

    // Action
    $userRepository = TestCase::createMock(IUserRepository::class);
    $authService = new AuthService($userRepository);

    $authService->authorization([
      'token' => $authorization
    ]);
  }

  #[Test]
  public function testTokenInvalido_SemInformarBearer() {
    // Arrange
    $token  = $this->tokenFactory();
    $authorization = $token;

    $this->expectException(ValidationException::class);

    // Action
    $userRepository = TestCase::createMock(IUserRepository::class);
    $authService = new AuthService($userRepository);

    $authService->authorization([
      'token' => $authorization
    ]);
  }

  #[Test]
  public function testTokenInvalido_RandomToken() {
    // Arrange
    $authorization = "Bearer ojisauhdibasjndaioshduaisdna.asdsfvgtrefewdcsfgbcdfg.dsfghnjty56y4grevfd";

    $this->expectException(ValidationException::class);

    // Action
    $userRepository = TestCase::createMock(IUserRepository::class);
    $authService = new AuthService($userRepository);

    $authService->authorization([
      'token' => $authorization
    ]);
  }

  #[Test]
  public function testTokenInvalido_BearerInvalido() {
    // Arrange
    $token  = $this->tokenFactory();
    $authorization = "Beareer $token";

    $this->expectException(ValidationException::class);

    // Action
    $userRepository = TestCase::createMock(IUserRepository::class);
    $authService = new AuthService($userRepository);

    $authService->authorization([
      'token' => $authorization
    ]);
  }

  private function tokenFactory() {
    $login = 'dan@gmail.com';
    $password = 'Abc123!@#';

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('findByLogin')
      ->with($login)
      ->willReturn(
        new User(
          id: 1,
          name: 'Dan Ruan',
          login: $login,
          password: md5('Abc123!@#'),
          active: true
        )
      );

    $authService = new AuthService($userRepository);

    $result = $authService->login([
      'login' => $login,
      'password' => $password,
    ]);

    return $result['token'];
  }
}
