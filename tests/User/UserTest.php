<?php
namespace Tests\User;

use App\Exception\Http\InternalServerErrorException;
use App\Model\User;
use App\Repository\IUserRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserTest extends TestCase{

  #[Test]
  public function deveAcharUsuario(){
    //Arrange
    $id = 1;

    $userRepository = TestCase::createMock(IUserRepository::class);

    $userRepository->method('findById')->with($id)->willReturn(new User([
      'id' => $id,
      'name' => 'Davi',
      'login' => 'davi323'
    ]));

    //Act

    $userService = new UserService($userRepository);

    $user = $userService->getById(['id' => $id]);

    $userComparacao = new User([
      'id' => 1,
      'name' => 'Davi',
      'login' => 'davi323'
    ]);

    //Assert

    assertEquals($userComparacao, $user['user']);
  }

  #[Test]
  public function deveCadastrarUsuario(){
    //Arrange
    $nome = 'Davi';
    $login = 'davi.fadriano@gmail.com';

    $user = new User([
      'name' => $nome,
      'login' => $login
    ]);
    //Act

    //Configuração do Mock
    $userRepository = TestCase::createMock(UserRepository::class);

    $userRepository->method('create')
      ->with($user)
        ->willReturn($user);
        
    $userService = new UserService($userRepository);
        
    //Inserting User
    $response = $userService->create([
      'name' => $nome,
      'login' => $login
    ]);

    assertEquals(['message' => 'Usuário cadastrado com sucesso'], $response);

  }
}