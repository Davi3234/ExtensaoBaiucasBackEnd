<?php

namespace Tests\User;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\IUserRepository;

class UserTest extends TestCase {

  #[Test]
  public function deveAcharUsuario() {
    //Arrange
    $id = 1;

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository
      ->method('findById')
      ->with($id)
      ->willReturn(
        new User([
          'id' => $id,
          'name' => 'Davi',
          'login' => 'davi323',
          'active' => true,
        ])
      );

    //Act
    $userService = new UserService($userRepository);

    $user = $userService->getById(['id' => $id]);

    //Assert
    $userComparacao = [
      'id' => 1,
      'name' => 'Davi',
      'login' => 'davi323',
      'active' => true,
    ];

    $this->assertEquals($userComparacao, $user['user']);
  }

  #[Test]
  public function deveCadastrarUsuario() {
    //Arrange
    $nome = 'Davi';
    $login = 'davi.fadriano@gmail.com';
    $password = 'davi123';

    $user = new User([
      'name' => $nome,
      'login' => $login,
      'password' => $password,
      'active' => true,
    ]);

    $userReturn = new User([
      'name' => $nome,
      'login' => $login,
      'password' => md5($password),
      'active' => true,
    ]);

    //Act

    //Configuração do Mock
    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('create')
      ->with($user)
      ->willReturn($userReturn);

    $userService = new UserService($userRepository);

    //Inserting User
    $response = $userService->create([
      'name' => $nome,
      'login' => $login,
      'password' => $password
    ]);

    //Assert
    $this->assertEquals(['message' => 'Usuário cadastrado com sucesso'], $response);
  }
}
