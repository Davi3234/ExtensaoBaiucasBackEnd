<?php

namespace Tests\Danrley\Auth;

use App\Enums\TipoUsuario;
use App\Models\User;
use App\Repositories\IUserRepository;
use App\Services\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

  #[Test]
  public function deveAtualizarUsuario() {
    // Arrange
    $dto = [
      'id' => 1,
      'name' => 'Davi',
      'login' => '',
      'cpf' => '645.549.820-84',
      'endereco' => 'Rua de Teste 2',
      'password' => 'Davi@432',
      'confirm_password' => 'Davi@432',
    ];

    // Action
    $userRepository = $this->createMock(IUserRepository::class);

    $userRepository
      ->method('findById')
      ->with($dto['id'])
      ->willReturn(new User(
        id: 1,
        name: 'Dan Ruan',
        login: 'dan@gmail.com',
        cpf: '489.945.080-07',
        endereco: 'Rua de Teste',
        password: 'Dan!@#123',
        active: true,
        tipo: TipoUsuario::ADMNISTRADOR,
      ));

    $userService = new UserService($userRepository);

    $result = $userService->update($dto);

    // Assertion
    $this->assertTrue(['message' => 'Usuário atualizado com sucesso'] == $result);
  }
}
