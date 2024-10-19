<?php
namespace Tests\User;

use App\Model\User;
use App\Provider\Zod\Z;
use App\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Mocks\UserMock;

use function PHPUnit\Framework\assertEquals;

class UserTest extends TestCase{

  #[Test]
  public function deveAcharUsuario(){
    $id = 1;

    $userService = new UserService(new UserMock());
    $user = $userService->getById(['id' => $id]);

    $userComparacao = new User([
      'id' => 1,
      'nome' => 'Davi', 
      'login' => 'davi323'
    ]);

    assertEquals($userComparacao, $user);
  }
}