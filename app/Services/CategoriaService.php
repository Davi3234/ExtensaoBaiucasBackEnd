<?php

namespace App\Services;

use App\Models\Categoria;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Repositories\ICategoriaRepository;
use App\Repositories\IProdutoRepository;
use Provider\Database\DatabaseException;

class CategoriaService {

  public function __construct(
    private readonly ICategoriaRepository $categoriaRepository,
    private readonly IProdutoRepository $produtoRepository
  ) {
  }

  public function query() {
    $categorias = $this->categoriaRepository->findMany();

    $raw = array_map(function ($categoria) {
      return [
        'id' => $categoria->getId(),
        'descricao' => $categoria->getDescricao()
      ];
    }, $categorias);

    return $raw;
  }

  public function queryProdutos() {
    $categorias = $this->categoriaRepository->findMany();

    $rawCategorias = [];
    foreach ($categorias as $categoria) {
      $produtos = $this->produtoRepository->findManyByIdCategoria($categoria->getId());

      $rawCategoria = [
        'id' => $categoria->getId(),
        'descricao' => $categoria->getDescricao(),
        'produtos' => array_map(function ($produto) {
          return [
            'id' => $produto->getIdProduto(),
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'valor' => $produto->getValor(),
            'descricao_categoria' => $produto->getCategoria()->getDescricao(),
            'ativo' => $produto->getAtivo(),
            'data_inclusao' => $produto->getDataInclusao(),
          ];
        }, $produtos),
      ];

      $rawCategorias[] = $rawCategoria;
    }

    return [
      'categorias' => $rawCategorias
    ];
  }

  /**
   * Retorna uma categoria buscando pelo seu ID
   * @param array $args
   * @throws \Exception\ValidationException
   * @return array{categoria: array{id: int, descricao: string}}
   */

  public function getById(array $args) {
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
        'id' => $categoria->getId(),
        'descricao' => $categoria->getDescricao()
      ]
    ];
  }

  public function create(array $args) {
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

  public function update(array $args) {
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

    $categoriaToUpdate->setDescricao($dto->descricao);

    $this->categoriaRepository->update($categoriaToUpdate);

    return ['message' => 'Categoria atualizada com sucesso'];
  }

  public function delete(array $args) {
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

    $categoriaToDelete = $this->categoriaRepository->findById($dto->id);

    if (!$categoriaToDelete) {
      throw new ValidationException('Não é possível excluir a categoria', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id'
        ]
      ]);
    }

    try {
      $this->categoriaRepository->deleteById($categoriaToDelete->getId());
    } catch (DatabaseException $err) {
      throw new DatabaseException($err->getMessage());
    }

    return ['message' => 'Categoria excluída com sucesso'];
  }
}
