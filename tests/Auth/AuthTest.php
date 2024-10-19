<?php

namespace Tests\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use App\Service\AuthService;

class AuthTest extends TestCase {

  #[Test]
  public function testDeveEfetuarLogin() {
    $login = 'dan@gmail.com';
    $password = 'Dan123!@#';

    $authService = new AuthService();

    $result = $authService->login([
      'login' => $login,
      'password' => $password,
    ]);

    $this->assertEquals($result['message'], 'Teste');
  }
}
