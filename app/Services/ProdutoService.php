<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Produto;
use App\Repositories\IProdutoRepository;

class ProdutoService {

  public function __construct(
    private readonly IProdutoRepository $produtoRepository
  ) {
  }

  public function query() {
    $produtos = $this->produtoRepository->findMany();

    if(count($produtos) == 0)
      throw new ValidationException('Nenhum Produto encontrado');

    $raw = array_map(function ($produto) {
      return [
        'id_produto' => $produto->getIdProduto(),
        'nome' => $produto->getNome(),
        'descricao' => $produto->getDescricao(),
        'valor' => $produto->getValor(),
        'id_categoria' => $produto->getIdCategoria(),
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
  public function getById(array $args) {
    $getSchema = Z::object([
      'id_produto' => Z::number(['required' => 'Id do Produto é obrigatório', 'invalidType' => 'Id do Produto inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Produto inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $produto =  $this->produtoRepository->findById($dto->id);

    if (!$produto)
      throw new ValidationException('Usuário não encontrado');

    return [
      'produto' => [
        'id_produto' => $produto->getIdProduto(),
        'nome' => $produto->getNome(),
        'descricao' => $produto->getDescricao(),
        'valor' => $produto->getValor(),
        'id_categoria' => $produto->getIdCategoria(),
        'ativo' => $produto->getAtivo(),
        'data_inclusao' => $produto->getDataInclusao(),
      ]
    ];
  }

  public function create(array $args) {
    $createSchema = Z::object([
      'nome' => Z::string(['required' => 'Nome é obrigatório']),
      'valor' => Z::string(['required' => 'Valor é obrigatório']),
      'id_categoria' => Z::string(['required' => 'Categoria é obrigatória']),
      'data_inclusao' => Z::string(['required' => 'Data de inclusão é obrigatória']),
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $produto = new Produto();

    $produto->setNome($dto->nome);
    $produto->setDescricao($dto->descricao);
    $produto->setValor($dto->valor);
    $produto->setIdCategoria($dto->id_categoria);
    $produto->setDataInclusao($dto->data_inclusao);
    $produto->setAtivo($dto->ativo);
    
    $this->produtoRepository->create($produto);

    return ['message' => 'Produto cadastrado com sucesso'];
  }

  public function update(array $args) {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Produto é obrigatório', 'invalidType' => 'Id do Produto inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Produto inválido')
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $produto = $this->produtoRepository->findById($dto->id_produto);

    if (!$produto) {
      throw new ValidationException(
        'Não é possível atualizar o Produto',
        [
          ['message' => 'Produto não encontrado', 'cause' => 'id_produto']
        ]
      );
    }

    //Altera tudo menos o Id do Produto
    $produto->setNome($dto->nome);
    $produto->setDescricao($dto->descricao);
    $produto->setValor($dto->valor);
    $produto->setIdCategoria($dto->id_categoria);
    $produto->setDataInclusao($dto->data_inclusao);
    $produto->setAtivo($dto->ativo);

    $this->produtoRepository->update($produto);

    return ['message' => 'Produto atualizado com sucesso'];
  }

  public function delete(array $args) {
    $deleteSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Produto é obrigatório', 'invalidType' => 'Id do Produto inválido'])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Produto inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $produtoToDelete = $this->getById($dto->id_produto)['produto'];

    if ($produtoToDelete) {
      throw new ValidationException(
        'Não é possível excluir o Produto',
        [
          ['message' => 'Produto não encontrado', 'cause' => 'id_produto']
        ]
      );
    }

    $this->produtoRepository->deleteById($dto->id_produto);

    return ['message' => 'Produto excluído com sucesso'];
  }
}
