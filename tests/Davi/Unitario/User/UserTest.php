<?php

namespace Tests\Davi\Unitario\User;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\IUserRepository;
use Provider\Zod\ZodParseException;

class UserTest extends TestCase
{

  #[Test]
  public function deveCriarUsuario(){
    //Arrange
    $nome = 'Davi';
    $login = 'davi@gmail.com';
    $cpf = '028.320.360-90';
    $endereco = 'Rua de Teste';
    $password = 'Davi1234!';
    $confirm_password = 'Davi1234!';

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true
    );

    //Act

    //Configuração do Mock
    $userRepository = $this->createMock(IUserRepository::class);

    $userRepository->method('create')
      ->with($user)
      ->willReturn($user);

    $userRepository->method('findByLogin')
      ->willReturn(null);

    $userService = new UserService($userRepository);

    $response = $userService->createUser([
      'name' => $nome,
      'login' => $login,
      'password' => $password,
      'confirm_password' => $confirm_password,
      'cpf' => $cpf,
      'endereco' => $endereco
    ]);

    //Assert
    $this->assertTrue(['message' => 'Usuário cadastrado com sucesso'] == $response);
  }

  #[Test]
  public function deveDispararExcecaoParaNomeInvalido(){

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = '';
    $login = 'davi@gmail.com';
    $cpf = '028.320.360-90';
    $endereco = 'Rua de Teste';
    $password = 'Davi1234!';
    $confirm_password = 'Davi1234!';

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true
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
      'endereco' => $endereco
    ]);
  }

  #[Test]
  public function deveDispararExcecaoParaCPFInvalido(){

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = 'Davi';
    $login = 'davi@gmail.com';
    $cpf = '123.123.123-99';
    $endereco = 'Rua de Teste';
    $password = 'Davi1234!';
    $confirm_password = 'Davi1234!';

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true
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
      'endereco' => $endereco
    ]);
  }

  #[Test]
  public function deveDispararExcecaoParaEmailInvalido(){

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = 'Davi';
    $login = 'davigmail.com';
    $cpf = '028.320.360-90';
    $endereco = 'Rua de Teste';
    $password = 'Davi1234!';
    $confirm_password = 'Davi1234!';

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true
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
      'endereco' => $endereco
    ]);
  }

  #[Test]
  public function deveDispararExcecaoParaForcaSenha(){

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = 'Davi';
    $login = 'davi@gmail.com';
    $cpf = '028.320.360-90';
    $endereco = 'Rua de Teste';
    $password = 'davi1234';
    $confirm_password = 'davi1234';

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true
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
      'endereco' => $endereco
    ]);
  }

  #[Test]
  public function deveDispararExcecaoParaValidarEndereco(){

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = 'Davi';
    $login = 'davi@gmail.com';
    $cpf = '028.320.360-90';
    $endereco = '';
    $password = 'davi1234';
    $confirm_password = 'davi1234';

    $user = new User(
      name: $nome,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: md5($password),
      active: true
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
      'endereco' => $endereco
    ]);
  }
}
