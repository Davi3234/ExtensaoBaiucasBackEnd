<?php

namespace Tests\User;

use App\Exception\CriticalException;
use App\Model\User;
use App\Repository\IUserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use App\Service\AuthService;

class UserRepositoryMock implements IUserRepository {
  function create(User $user): User {
    throw new CriticalException('Method not implemented');
  }
  function update(User $user): User {
    throw new CriticalException('Method not implemented');
  }
  function deleteById(int $id): User {
    throw new CriticalException('Method not implemented');
  }

  /**
   * @return User[]
   */
  function findMany(): array {
    throw new CriticalException('Method not implemented');
  }
  function findByLogin(string $login): ?User {
    return User::__loadModel(['id' => 1, 'name' => 'Dan Ruan', 'login' => $login]);
  }
  function findById(int $id): ?User {
    throw new CriticalException('Method not implemented');
  }
}

class AuthTest extends TestCase {

  #[Test]
  public function testDeveEfetuarLogin() {
    $login = 'dan@gmail.com';
    $password = 'Dan123!@#';

    $authService = new AuthService(new UserRepositoryMock());

    $result = $authService->login([
      'login' => $login,
      'password' => $password,
    ]);

    $this->assertEquals($result['payload']['sub'], 1);
  }
}
