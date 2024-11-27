<?php

namespace Tests\Davi\User;

use App\Enums\TipoUsuario;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\IUserRepository;

class UserTest extends TestCase
{

  #[Test]
  public function deveAcharUsuario()
  {
    //Arrange
    $id = 1;

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository
      ->method('findById')
      ->with($id)
      ->willReturn(
        new User(
          id: 1,
          name: 'Davi',
          login: 'davi323',
          active: true,
          tipo: TipoUsuario::CLIENTE
        )
      );

    //Act
    $userService = new UserService($userRepository);

    $user = $userService->getById(['id' => $id]);

    //Assert
    $userComparacao = [
      'user' => [
        'id' => 1,
        'name' => 'Davi',
        'login' => 'davi323',
        'active' => true,
        'tipo' => TipoUsuario::CLIENTE
      ]
    ];

    $this->assertEquals($userComparacao, $user);
  }

  #[Test]
  public function deveCadastrarUsuario()
  {
    //Arrange
    $nome = 'Davi';
    $login = 'davi.fadriano@gmail.com';
    $password = 'Davi!@#123';
    $confirm_password = 'Davi!@#123';
    $tipo = TipoUsuario::CLIENTE->value;

    $user = new User(
      name: $nome,
      login: $login,
      password: md5($password),
      active: true,
    );

    //Act

    //Configuração do Mock
    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('create')
      ->with($user)
      ->willReturn($user);

    $userRepository->method('findByLogin')
      ->willReturn(null);

    $userService = new UserService($userRepository);

    //Inserting User
    $response = $userService->create([
      'name' => $nome,
      'login' => $login,
      'password' => $password,
      'confirm_password' => $confirm_password,
      'tipo' => $tipo,
    ]);

    //Assert
    $this->assertEquals(['message' => 'Usuário cadastrado com sucesso'], $response);
  }
}
