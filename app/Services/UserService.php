<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\User;
use App\Repositories\IUserRepository;

class UserService {

  public function __construct(
    private readonly IUserRepository $userRepository
  ) {
  }

  public function query() {
    $users = $this->userRepository->findMany();

    $raw = array_map(function ($user) {
      return [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'login' => $user->getLogin(),
        'active' => $user->getActive(),
      ];
    }, $users);

    return $raw;
  }

  public function getById(array $args) {
    $getSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Usuário é obrigatório',
        'invalidType' => 'Id do Usuário inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $user =  $this->userRepository->findById($dto->id);

    if (!$user)
      throw new ValidationException('Não foi possível encontrar o Usuário', [
        [
          'message' => 'Usuário não encontrado',
          'origin' => 'id'
        ]
      ]);

    return [
      'user' => [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'login' => $user->getLogin(),
        'active' => $user->getActive(),
      ]
    ];
  }

  public function create(array $args) {
    $createSchema = Z::object([
      'name' => Z::string([
        'required' => 'Nome é obrigatório'
      ])
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
      'login' => Z::string(['required' => 'Login é obrigatório'])
        ->trim()
        ->regex('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'Login invalid'),
      'password' => Z::string(['required' => 'Senha é obrigatório'])
        ->trim(),
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $userWithSameLogin = $this->userRepository->findByLogin($dto->login);

    if ($userWithSameLogin) {
      throw new ValidationException('Não foi possível cadastrar o Usuário', [
        [
          'message' => 'Já existe um Usuário com o mesmo login informado',
          'origin' => 'login'
        ]
      ]);
    }

    $user = new User();

    $user->setName($dto->name);
    $user->setLogin($dto->login);
    $user->setPassword(md5($dto->password));
    $user->setActive(true);

    $this->userRepository->create($user);

    return ['message' => 'Usuário cadastrado com sucesso'];
  }

  public function update(array $args) {
    $updateSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Usuário é obrigatório',
        'invalidType' => 'Id do Usuário inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido'),
      'name' => Z::string(['required' => 'Nome é obrigatório'])
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $user = $this->userRepository->findById($dto->id);

    if (!$user) {
      throw new ValidationException('Não foi possível atualizar o Usuário', [
        [
          'message' => 'Usuário não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    $user->setName($dto->name);

    $this->userRepository->update($user);

    return ['message' => 'Usuário atualizado com sucesso'];
  }

  public function delete(array $args) {
    $deleteSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Usuário é obrigatório',
        'invalidType' => 'Id do Usuário inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $userToDelete = $this->getById($dto->id)['user'];

    if ($userToDelete) {
      throw new ValidationException('Não foi possível excluir o Usuário', [
        [
          'message' => 'Usuário não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    $this->userRepository->deleteById($dto->id);

    return ['message' => 'Usuário excluído com sucesso'];
  }
}
