<?php

namespace App\Service;

use App\Exception\Http\BadRequestException;
use App\Model\User;
use App\Provider\Zod\Z;
use App\Repository\IUserRepository;

class UserService {

  function __construct(
    private readonly IUserRepository $userRepository
  ) {
  }

  function query() {
    $users = $this->userRepository->findMany();

    $raw = array_map(function($user) {
      return [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'login' => $user->getLogin(),
      ];
    }, $users);

    return $raw;
  }

  function getById($args) {
    $deleteSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
            ->coerce()
            ->int()
            ->gt(0, 'Id do Usuário inválido')
    ]);

    $dto = $deleteSchema->parseNoSafe($args);

    $user =  $this->userRepository->findById($dto->id);

    return ['user' => $user];
  }

  function create(array $args) {
    $createSchema = Z::object([
      'name' => Z::string(['required' => 'Nome é obrigatório'])
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
      'login' => Z::string(['required' => 'Login é obrigatório'])
        ->trim()
        ->regex('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'Login invalid'),
    ]);

    $dto = $createSchema->parseNoSafe($args);
    
    $userWithSameLogin = $this->userRepository->findByLogin($dto->login);

    if ($userWithSameLogin) {
      throw new BadRequestException(
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

  function update(array $args) {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Usuário inválido'),
      'name' => Z::string(['required' => 'Nome é obrigatório'])
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
    ]);

    $dto = $updateSchema->parseNoSafe($args);
    
    $user = $this->userRepository->findById($dto->id);

    if (!$user) {
      throw new BadRequestException(
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

  function delete(array $args) {
    $deleteSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório', 'invalidType' => 'Id do Usuário inválido'])
      ->coerce()
      ->int()
      ->gt(0, 'Id do Usuário inválido')
    ]);

    $dto = $deleteSchema->parseNoSafe($args);

    $userToDelete = $this->getById($dto->id);

    if ($userToDelete) {
      throw new BadRequestException(
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
