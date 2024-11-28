<?php

namespace Tests\Danrley\Auth;

use App\Enums\TipoUsuario;
use App\Models\User;
use App\Repositories\IUserRepository;
use App\Services\UserService;
use Core\Exception\Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Provider\Zod\ZodParseException;

class UserTest extends TestCase {

  #[Test]
  public function deveAtualizarUsuario() {
    $dto = [
      'id' => 1,
      'name' => 'Davi',
      'login' => '',
      'cpf' => '645.549.820-84',
      'endereco' => 'Rua de Teste 2',
      'password' => 'Davi@432',
      'confirm_password' => 'Davi@432',
    ];

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

    $this->assertTrue(['message' => 'Usuário atualizado com sucesso'] == $result);
  }

  #[Test]
  public function naoDeveAtualizarUsuarioComNomeInvalido() {
    $dto = [
      'id' => 1,
      'name' => 'Da',
      'login' => '',
      'cpf' => '645.549.820-84',
      'endereco' => 'Rua de Teste 2',
      'password' => 'Davi@432',
      'confirm_password' => 'Davi@432',
    ];

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

    $this->expectException(ZodParseException::class);

    try {
      $userService->update($dto);
    } catch (Exception $err) {
      $this->assertNotEmpty($err->getCausesFromOrigin('name'));

      throw $err;
    }
  }
}
