<?php

namespace App\Services;

use App\Models\Categoria;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Repositories\ICategoriaRepository;

class CategoriaService {

  public function __construct(
    private readonly ICategoriaRepository $categoriaRepository
  ) {
  }

  public function query() {
    $categorias = $this->categoriaRepository->findMany();

    $raw = array_map(function ($categoria) {
      return [
        'id_categoria' => $categoria->getIdCategoria(),
        'descricao_categoria' => $categoria->getDescricaoCategoria()
      ];
    }, $categorias);

    return $raw;
  }

  /**
   * Array de categoria
   * @param array $args
   * @return array
   */

  public function getById(array $args) {
    $getSchema = Z::object([
      'id_categoria' => Z::number([
        'required' => 'Id da Categoria é obrigatório',
        'invalidType' => 'Id da Categoria inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id da Categoria inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $categoria =  $this->categoriaRepository->findById($dto->id_categoria);

    if (!$categoria)
      throw new ValidationException('Não foi possível encontrar a Categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id_categoria'
        ]
      ]);

    return [
      'categoria' => [
        'id_categoria' => $categoria->getIdCategoria(),
        'descricao_categoria' => $categoria->getDescricaoCategoria()
      ]
    ];
  }

  public function create(array $args) {
    $createSchema = Z::object([
      'id_categoria' => Z::string(['required' => 'Id da Categoria é obrigatório!'])
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $categoriaArgs = $this->getById($dto->id_categoria);

    if(!$categoriaArgs){
      throw new ValidationException('Não foi possível atualizar a Categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'categoria'
        ]
      ]);
    }

    $categoria = new Categoria(
      id_categoria: $dto->id_categoria,
      descricao_categoria: $dto->descricao_categoria
    );

    $this->categoriaRepository->create($categoria);

    return ['message' => 'Categoria inserida com sucesso!'];
  }
  
  public function update(array $args) {
    $updateSchema = Z::object([
      'id_categoria' => Z::number([
        'required' => 'Id da Categoria é obrigatório',
        'invalidType' => 'Id da Categoria inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id da Categoria inválido')
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $categoriaToUpdate = $this->categoriaRepository->findById($dto->id_categoria);

    if (!$categoriaToUpdate) {
      throw new ValidationException('Não foi possível atualizar a Categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id_categoria'
        ]
      ]);
    }

    $categoriaToUpdate->setDescricaoCategoria($dto->descricao_categoria);

    $this->categoriaRepository->update($categoriaToUpdate);

    return ['message' => 'Categoria atualizada com sucesso'];
  }

  public function delete(array $args) {
    $deleteSchema = Z::object([
      'id_categoria' => Z::number([
        'required' => 'Id da Categoria é obrigatório',
        'invalidType' => 'Id da Categoria inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id da Categoria inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $categoriaToDelete = $this->getById($dto->id_categoria)['categoria'];

    if ($categoriaToDelete) {
      throw new ValidationException('Não é possível excluir a categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id_categoria'
        ]
      ]);
    }

    $this->categoriaRepository->deleteById($dto->id_categoria);

    return ['message' => 'Categoria excluída com sucesso'];
  }
}
