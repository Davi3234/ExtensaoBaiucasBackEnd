<?php

namespace App\Services;

use App\Models\Categoria;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Repositories\ICategoriaRepository;
use Provider\Database\DatabaseException;

class CategoriaService
{

  public function __construct(
    private readonly ICategoriaRepository $categoriaRepository
  ) {}

  public function query()
  {
    $categorias = $this->categoriaRepository->findMany();

    $raw = array_map(function ($categoria) {
      return [
        'id' => $categoria->getIdCategoria(),
        'descricao' => $categoria->getDescricaoCategoria()
      ];
    }, $categorias);

    return $raw;
  }

  /**
   * Array de categoria
   * @param array $args
   * @return array
   */

  public function getById(array $args)
  {
    $getSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id da Categoria é obrigatório',
        'invalidType' => 'Id da Categoria inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id da Categoria inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $categoria =  $this->categoriaRepository->findById($dto->id);

    if (!$categoria)
      throw new ValidationException('Não foi possível encontrar a Categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id'
        ]
      ]);

    return [
      'categoria' => [
        'id' => $categoria->getIdCategoria(),
        'descricao' => $categoria->getDescricaoCategoria()
      ]
    ];
  }

  public function create(array $args)
  {
    $createSchema = Z::object([
      'descricao' => Z::string(['required' => 'Descrição da categoria é obrigatória!'])
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $categoriaToInsert = $this->categoriaRepository->findByDescription($dto->descricao);

    if ($categoriaToInsert) {
      throw new ValidationException('Não foi possível cadastrar a Categoria', [
        [
          'message' => 'Já existe uma Categoria com a mesma descrição informada',
          'origin' => 'descricao'
        ]
      ]);
    }

    $categoria = new Categoria(
      descricao: $dto->descricao
    );

    $this->categoriaRepository->create($categoria);

    return ['message' => 'Categoria inserida com sucesso!'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id da Categoria é obrigatório!'])
        ->coerce()
        ->int(),
      'descricao' => Z::string(['required' => 'Descrição da categoria é obrigatória!'])
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $categoriaToUpdate = $this->categoriaRepository->findById($dto->id);

    if (!$categoriaToUpdate) {
      throw new ValidationException('Não foi possível atualizar a Categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id'
        ]
      ]);
    }

    $categoriaToUpdate->setDescricaoCategoria($dto->descricao);

    $this->categoriaRepository->update($categoriaToUpdate);

    return ['message' => 'Categoria atualizada com sucesso'];
  }

  public function delete(array $args)
  {
    $deleteSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id da Categoria é obrigatório',
        'invalidType' => 'Id da Categoria inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id da Categoria inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $categoriaToDelete = $this->getById(['id' => $dto->id])['categoria'];

    if (!$categoriaToDelete) {
      throw new ValidationException('Não é possível excluir a categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id'
        ]
      ]);
    }

    try {
      $this->categoriaRepository->deleteById($dto->id);
    } catch (DatabaseException $th) {
      throw new DatabaseException($th->getMessage());
    }

    return ['message' => 'Categoria excluída com sucesso'];
  }
}
