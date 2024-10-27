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

  /**
   * Array de usuário
   * @param array $args
   * @return array
   */
  public function getById(array $args) {
    $getSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $user =  $this->userRepository->findById($dto->id);

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
      'name' => Z::string(['required' => 'Nome é obrigatório'])
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
      'login' => Z::string(['required' => 'Login é obrigatório'])
        ->trim()
        ->regex('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'Login invalid'),
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $userWithSameLogin = $this->userRepository->findByLogin($dto->login);

    if ($userWithSameLogin) {
      throw new ValidationException(
        'Não é possível cadastrar o usuário',
        [
          ['message' => 'Já existe um usuário com o mesmo login informado', 'cause' => 'login']
        ]
      );
    }

    $user = new User();

    $user->setName($dto->name);
    $user->setLogin($dto->login);

    $this->userRepository->create($user);

    return ['message' => 'Usuário cadastrado com sucesso'];
  }

  public function update(array $args) {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
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
      throw new ValidationException(
        'Não é possível atualizar o usuário',
        [
          ['message' => 'Usuário não encontrado', 'cause' => 'id']
        ]
      );
    }

    $user->setName($dto->name);

    $this->userRepository->update($user);

    return ['message' => 'Usuário atualizado com sucesso'];
  }

  public function delete(array $args) {
    $deleteSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $userToDelete = $this->getById($dto->id)['user'];

    if ($userToDelete) {
      throw new ValidationException(
        'Não é possível excluir o usuário',
        [
          ['message' => 'Usuário não encontrado', 'cause' => 'id']
        ]
      );
    }

    $this->userRepository->deleteById($dto->id);

    return ['message' => 'Usuário excluído com sucesso'];
  }
}
