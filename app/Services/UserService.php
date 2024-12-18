<?php

namespace App\Services;

use App\Enums\TipoUsuario;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\User;
use App\Repositories\IUserRepository;
use Provider\Database\DatabaseException;

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
   * Retorna um usuário buscando pelo seu ID
   * @param array $args
   * @throws \Exception\ValidationException
   * @return array{user: array{ active: bool, id: int, login: string, name: string, tipo: string}}
   */
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

    if (!$user) {
      throw new ValidationException('Não foi possível encontrar o Usuário', [
        [
          'message' => 'Usuário não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    return [
      'user' => [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'login' => $user->getLogin(),
        'active' => $user->getActive(),
        'tipo' => $user->getTipo(),
      ]
    ];
  }

  public function createUser(array $args) {
    $createSchema = Z::object([
      'name' => Z::string([
        'required' => 'Nome é de preenchimento obrigatório'
      ])
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
      'login' => Z::string(['required' => 'Login é de preenchimento obrigatório'])
        ->trim()
        ->regex('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'Login inválido'),
      'cpf' => Z::string(['required' => 'CPF é de preenchimento obrigatório'])
        ->trim()
        ->refine(
          function ($cpf) {
            $cpf = preg_replace('/\D/', '', $cpf);

            if (strlen($cpf) != 11) {
              return false;
            }

            if (preg_match('/^(\d)\1*$/', $cpf)) {
              return false;
            }

            for ($t = 9; $t < 11; $t++) {
              $soma = 0;
              for ($i = 0; $i < $t; $i++) {
                $soma += $cpf[$i] * (($t + 1) - $i);
              }

              $digito = ($soma * 10) % 11;
              $digito = ($digito == 10 || $digito == 11) ? 0 : $digito;

              if ($digito != $cpf[$t]) {
                return false;
              }
            }

            return true;
          },
          'CPF é inválido'
        ),
      'endereco' => Z::string(['required' => 'Endereço é de preenchimento obrigatório'])
        ->trim()
        ->min(3, 'Endereço precisa ter no mínimo 3 caracteres'),
      'password' => Z::string(['required' => 'Senha é de preenchimento obrigatório'])
        ->trim()
        ->min(8, 'Utilize ao menos 8 caracteres')
        ->regex('/^(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/', 'Utilize ao menos um símbolo especial, uma letra maiúscula e um número'),
      'confirm_password' => Z::string(['required' => 'Confirmação de senha é de preenchimento obrigatório'])
        ->trim(),
      'tipo' => Z::enumNative(TipoUsuario::class, ['required' => 'Tipo é de preenchimento obrigatório'])
        ->defaultValue(TipoUsuario::CLIENTE->value)
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $userWithSameLogin = $this->userRepository->findByLogin($dto->login);

    if ($userWithSameLogin) {
      throw new ValidationException('Não foi possível cadastrar o Usuário', [
        [
          'message' => 'Já existe um Usuário com o mesmo login informado',
          'origin' => 'user'
        ]
      ]);
    }

    if ($dto->password != $dto->confirm_password) {
      throw new ValidationException('Não foi possível cadastrar o Usuário', [
        [
          'message' => 'A senha deve ser igual a confirmação de senha',
          'origin' => 'password'
        ]
      ]);
    }

    $user = new User();

    $user->setName($dto->name);
    $user->setLogin($dto->login);
    $user->setPassword(md5($dto->password));
    $user->setCpf($dto->cpf);
    $user->setEndereco($dto->endereco);
    $user->setActive(true);
    $user->setTipo(TipoUsuario::tryFrom($dto->tipo));

    $this->userRepository->create($user);

    return ['message' => 'Usuário cadastrado com sucesso'];
  }

  public function update(array $args) {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Usuário é obrigatório'])
        ->coerce()
        ->int(),
      'name' => Z::string()
        ->optional()
        ->trim()
        ->min(3, 'Nome precisa ter no mínimo 3 caracteres'),
      'login' => Z::string(['required' => 'Login é de preenchimento obrigatório'])
        ->optional()
        ->trim()
        ->regex('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'Login inválido'),
      'cpf' => Z::string()
        ->optional()
        ->trim()
        ->refine(
          function ($cpf) {
            $cpf = preg_replace('/\D/', '', $cpf);

            if (strlen($cpf) != 11) {
              return false;
            }

            if (preg_match('/^(\d)\1*$/', $cpf)) {
              return false;
            }

            for ($t = 9; $t < 11; $t++) {
              $soma = 0;
              for ($i = 0; $i < $t; $i++) {
                $soma += $cpf[$i] * (($t + 1) - $i);
              }

              $digito = ($soma * 10) % 11;
              $digito = ($digito == 10 || $digito == 11) ? 0 : $digito;

              if ($digito != $cpf[$t]) {
                return false;
              }
            }

            return true;
          },
          'CPF é inválido'
        ),
      'endereco' => Z::string()
        ->optional()
        ->trim()
        ->min(3, 'Endereço precisa ter no mínimo 3 caracteres'),
      'password' => Z::string()
        ->optional()
        ->min(8, 'Utilize ao menos 8 caracteres')
        ->regex('/^(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/', 'Utilize ao menos um símbolo especial, uma letra maiúscula e um número'),
      'confirm_password' => Z::string()
        ->optional()
        ->trim(),
      'tipo' => Z::enumNative(TipoUsuario::class)
        ->optional(),
      'active' => Z::boolean()
        ->coerce()
        ->optional()
    ])
      ->coerce()
      ->refine(function ($value) {
        return $value->password == $value->confirm_password;
      }, ['message' => 'A nova senha deve ser igual a confirmação de senha', 'origin' => 'confirm_password']);

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

    if ($dto->login) {
      $userWithSameLogin = $this->userRepository->findByLogin($dto->login);

      if ($userWithSameLogin && $userWithSameLogin->getId() != $dto->id) {
        throw new ValidationException('Não foi possível atualizar o Usuário', [
          [
            'message' => 'Já existe um Usuário com o mesmo login informado',
            'origin' => 'login'
          ]
        ]);
      }

      $user->setLogin($dto->login);
    }

    if ($dto->password) {
      $user->setPassword($dto->password);
    }

    if ($dto->name) {
      $user->setName($dto->name);
    }

    if ($dto->active) {
      $user->setActive($dto->active);
    }

    if ($dto->tipo) {
      $user->setTipo(TipoUsuario::tryFrom($dto->tipo));
    }

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

    $userToDelete = $this->userRepository->findById($dto->id);

    if (!$userToDelete) {
      throw new ValidationException('Não foi possível excluir o Usuário', [
        [
          'message' => 'Usuário não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    try {
      $this->userRepository->deleteById($userToDelete->getId());
    } catch (DatabaseException $err) {
      throw new DatabaseException($err->getMessage());
    }

    return ['message' => 'Usuário excluído com sucesso'];
  }
}
