<?php

namespace App\Services;

use App\Repositories\ICategoriaRepository;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Produto;
use App\Repositories\IProdutoRepository;

class ProdutoService
{

  public function __construct(
    private readonly IProdutoRepository $produtoRepository,
    private readonly ICategoriaRepository $categoriaRepository
  ) {}

  public function query()
  {
    $produtos = $this->produtoRepository->findMany();

    $raw = array_map(function ($produto) {
      return [
        'id' => $produto->getIdProduto(),
        'nome' => $produto->getNome(),
        'descricao' => $produto->getDescricao(),
        'valor' => $produto->getValor(),
        'id_categoria' => $produto->getCategoria()->getId(),
        'descricao_categoria' => $produto->getCategoria()->getDescricao(),
        'ativo' => $produto->getAtivo(),
        'data_inclusao' => $produto->getDataInclusao(),
      ];
    }, $produtos);

    return $raw;
  }

  /**
   * Array de usuário
   * @param array $args
   * @return array
   */
  public function getById(array $args)
  {
    $getSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Produto é obrigatório'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $produto =  $this->produtoRepository->findById($dto->id);

    if (!$produto)
      throw new ValidationException('Não foi possível encontrar o Produto', [
        [
          'message' => 'Produto não encontrado',
          'origin' => 'id'
        ]
      ]);

    return [
      'product' => [
        'id' => $produto->getIdProduto(),
        'name' => $produto->getNome(),
        'description' => $produto->getDescricao(),
        'value' => $produto->getValor(),
        'category' => [
          'id' => $produto->getCategoria()->getId()
        ],
        'ativo' => $produto->getAtivo(),
        'data_inclusao' => $produto->getDataInclusao(),
      ]
    ];
  }

  public function create(array $args)
  {
    $createSchema = Z::object([
      'nome' => Z::string(['required' => 'Nome é obrigatório']),
      'valor' => Z::number(['required' => 'Valor é obrigatório'])
        ->coerce()
        ->int(),
      'descricao' => Z::string(['required' => 'Descrição é obrigatória']),
      'id_categoria' => Z::number(['required' => 'Categoria é obrigatória'])
        ->coerce()
        ->int(),
      'data_inclusao' => Z::string(['required' => 'Data de inclusão é obrigatória'])
        ->defaultValue(date('Y-m-d')),
      'ativo' => Z::boolean(['required' => 'Ativo é obrigatório'])
        ->defaultValue(true),
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $ProdutoToInsert = $this->produtoRepository->findByDescription($dto->descricao);

    if ($ProdutoToInsert) {
      throw new ValidationException('Não foi possível cadastrar o Produto', [
        [
          'message' => 'Já existe um Produto com a mesma descrição informada',
          'origin' => 'descricao'
        ]
      ]);
    }

    $categoria =  $this->categoriaRepository->findById($dto->id_categoria);

    if (!$categoria) {
      throw new ValidationException('Não foi possível inserir o Produto', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id_categoria'
        ]
      ]);
    }

    $produto = new Produto();

    $produto->setNome($dto->nome);
    $produto->setDescricao($dto->descricao);
    $produto->setValor($dto->valor);
    $produto->setCategoria($categoria);
    $produto->setDataInclusao($dto->data_inclusao);
    $produto->setAtivo($dto->ativo);

    $this->produtoRepository->create($produto);

    return ['message' => 'Produto cadastrado com sucesso'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do produto é obrigatório'])
        ->coerce()
        ->int(),
      'nome' => Z::string(['required' => 'Nome é obrigatório']),
      'valor' => Z::number(['required' => 'Valor é obrigatório'])
        ->coerce()
        ->int(),
      'descricao' => Z::string(['required' => 'Descrição é obrigatória']),
      'id_categoria' => Z::number(['required' => 'Categoria é obrigatória'])
        ->coerce()
        ->int(),
      'data_inclusao' => Z::string(['required' => 'Data de inclusão é obrigatória'])
        ->defaultValue(date('Y-m-d')),
      'ativo' => Z::boolean(['required' => 'Ativo é obrigatório'])
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $produto = $this->produtoRepository->findById($dto->id);

    if (!$produto) {
      throw new ValidationException('Não foi possível atualizar o Produto', [
        [
          'message' => 'Produto não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    $categoria = $this->categoriaRepository->findById($dto->id_categoria);

    if (!$categoria) {
      throw new ValidationException('Não foi possível atualizar o Produto', [
        [
          'message' => 'Categoria não encontrada',
          'origin' => 'id'
        ]
      ]);
    }

    $ProdutoToUpdate = $this->produtoRepository->findByDescription($dto->descricao);

    if ($ProdutoToUpdate) {
      throw new ValidationException('Não foi possível atualizar o Produto', [
        [
          'message' => 'Já existe um Produto com a mesma descrição informada',
          'origin' => 'descricao'
        ]
      ]);
    }

    $produto->setNome($dto->nome);
    $produto->setDescricao($dto->descricao);
    $produto->setValor($dto->valor);
    $produto->setCategoria($categoria);
    $produto->setDataInclusao($dto->data_inclusao);
    $produto->setAtivo($dto->ativo);

    $this->produtoRepository->update($produto);

    return ['message' => 'Produto atualizado com sucesso'];
  }

  public function delete(array $args)
  {
    $deleteSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Produto é obrigatório',
        'invalidType' => 'Id do Produto inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Produto inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $produtoToDelete = $this->produtoRepository->findById($dto->id);

    if (!$produtoToDelete) {
      throw new ValidationException('Não foi possível excluir o Produto', [
        [
          'message' => 'Produto não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    $this->produtoRepository->deleteById($produtoToDelete->getIdProduto());

    return ['message' => 'Produto excluído com sucesso'];
  }
}
