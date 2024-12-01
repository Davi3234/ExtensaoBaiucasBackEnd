<?php

namespace Tests\Davi\User;

use App\Enums\TipoUsuario;
use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use PDO;
use PHPUnit\Framework\Attributes\Test;

class UserIntegracaoTest extends TestCase
{
  private UserRepository $userRepository;

  protected function setUp(): void{
    $this->userRepository = new UserRepository();
  }

  protected function insereDoisUsuarios(){
    $this->userRepository->create(new User(
      name: 'Davi',
      login: 'davi@gmail.com',
      cpf: '595.729.310-59',
      endereco: 'Rua teste',
      password: 'Davi123!',
      active: true,
      tipo: TipoUsuario::CLIENTE
    ));

    $this->userRepository->create(new User(
      name: 'João',
      login: 'joao@gmail.com',
      cpf: '640.885.620-97',
      endereco: 'Rua teste',
      password: 'Joao123!',
      active: true,
      tipo: TipoUsuario::CLIENTE
    ));
  }

  #[Test]
  public function testDeveListarUsuarios(){

    $this->insereDoisUsuarios();

    //Act
    $usuarios = $this->userRepository->findMany();

    //Assert
    $this->assertCount(2, $usuarios);
    $this->assertEquals('Davi', $usuarios[0]->getName());
    $this->assertEquals('João', $usuarios[1]->getName());
  }

  #[Test]
  public function testDeveAtualizarUsuario(){

    //Arrange
    $id = 1;
    $user = $this->userRepository->findById($id);

    //Act
    $userAct = $this->userRepository->update($user);

    //Assert
    $this->assertEquals($userAct->getId(), $id);

  }

  #[Test]
  public function testDeveCriarUsuario(){
    //Arrange
    $name = 'Davi';
    $login = 'davi@gmail.com';
    $cpf = '595.729.310-59';
    $endereco = 'Rua teste';
    $password = 'Davi123!';
    $active = true;
    $tipo = TipoUsuario::CLIENTE;

    $user = new User(
      name: $name,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: $password,
      active: $active,
      tipo: $tipo
    );

    //Act
    $userAct = $this->userRepository->create($user);

    //Assert
    $this->assertEquals($userAct, $user);

  }

  #[Test]
  public function testDeveRemoverUsuario(){
    //Arrange
    $id = 1;

    //Act
    $this->userRepository->deleteById($id);

    $userDeleted = $this->userRepository->findById($id);

    //Assert
    $this->assertEquals($userDeleted, null);

  }
}
