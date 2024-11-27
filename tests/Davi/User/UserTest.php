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
  public function deveCriarUsuario()
  {
    //Arrange
    $nome = 'Davi';
    $login = 'davi.fadriano@gmail.com';
    $cpf = '02832036090';
    $endereco = 'Rua de Teste';
    $password = 'Davi1234!';
    $confirm_password = 'Davi1234!';
    $tipo = TipoUsuario::CLIENTE->value;

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true,
      tipo: $tipo
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
    $response = $userService->createUser([
      'name' => $nome,
      'login' => $login,
      'password' => $password,
      'confirm_password' => $confirm_password,
      'cpf' => $cpf,
      'endereco' => $endereco,
      'tipo' => $tipo,
    ]);

    //Assert
    $this->assertEquals(['message' => 'Usuário cadastrado com sucesso'], $response);
  }
}
