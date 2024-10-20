<?php

namespace Tests\Mocks;

use App\Model\User;
use App\Repository\IUserRepository;
use Override;

class UserMock implements IUserRepository {

  protected $users = [];

  public function __construct() {
    $this->users = [
      new User([
        'id' =>   1,
        'nome' => "Davi",
        'login' => "davi3232",
        'password' => md5('Abc123!@#')
      ]),
      new User([
        'id' =>   2,
        'nome' => "Daiane",
        'login' => "daianegamer",
        'password' => md5('Abc123!@#')
      ]),
      new User([
        'id' => 3,
        'nome' => "Danrley",
        'login' => "danrleygamer",
        'password' => md5('Abc123!@#')
      ])
    ];
  }

  #[Override]
  public function create(User $user): User {
    array_push($this->users, $user);
    return $user;
  }

  #[Override]
  public function deleteById(int $id): User {

    $user = $this->findById($id);

    $this->users = array_filter($this->users, function ($user) use ($id) {
      return $user->getId() !== $id;
    });

    return $user;
  }

  #[Override]
  public function findById(int $id): ?User {
    foreach ($this->users as $user) {
      if ($user->getId() == $id) {
        return $user;
      }
    }

    return null;
  }

  #[Override]
  public function findByLogin(string $login): ?User {
    return new User();
  }

  #[Override]
  public function findMany(): array {
    return [];
  }

  #[Override]
  public function update(User $user): User {
    return $user;
  }
}
