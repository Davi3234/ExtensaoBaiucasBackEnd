<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\PedidoItem;
use App\Repositories\IPedidoItemRepository;
use App\Repositories\IPedidoRepository;
use App\Repositories\IProdutoRepository;

class PedidoItemService
{

  public function __construct(
    private readonly IPedidoItemRepository $pedidoItemRepository,
    private readonly IProdutoRepository $produtoRepository,
    private readonly IPedidoRepository $pedidoRepository
  ) {}

  public function query()
  {
    $itens = $this->pedidoItemRepository->findMany();

    $raw = array_map(function ($item) {
      return [
        'id' => $item->getId(),
        'id_pedido' => $item->getPedido()->getIdPedido(),
        'id_produto' => $item->getProduto()->getIdProduto(),
        'nome_produto' => $item->getProduto()->getNome(),
        'valor_item' => $item->getValorItem(),
        'observacoes_item' => $item->getObservacoesItem(),
      ];
    }, $itens);

    return $raw;
  }

  /**
   * Array de itens
   * @param array $args
   * @return array
   */

  public function getById(array $args)
  {
    $getSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Item é obrigatório',
        'invalidType' => 'Id do Item inválido'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $item =  $this->pedidoItemRepository->findById($dto->id);

    if (!$item)
      throw new ValidationException('Não foi possível encontrar o Item do Pedido', [
        [
          'message' => 'Item não encontrado',
          'origin' => 'id'
        ]
      ]);

    return [
      'item' => [
        'id' => $item->getId(),
        'id_pedido' => $item->getPedido()->getIdPedido(),
        'id_produto' => $item->getProduto()->getIdProduto(),
        'nome_produto' => $item->getProduto()->getNome(),
        'valor_item' => $item->getValorItem(),
        'observacoes_item' => $item->getObservacoesItem(),
      ]
    ];
  }

  /**
   * @return PedidoItem[]
   */
  public function findManyByIdPed(int $id): array
  {
    return $this->pedidoItemRepository->findManyByIdPedido($id);
  }

  public function create(array $args)
  {
    $createSchema = Z::object([
      'product' =>Z::object([
        'id' => Z::number(['required' => 'O item é de preenchimento obrigatório']),
      ])->coerce(),
      'id_pedido' => Z::number(['required' => 'Id do Pedido é obrigatório!'])->coerce()->coerce()->int(),
      'valor_item' => Z::number(['required' => 'Valor do ítem é obrigatório!'])->coerce(),
      'observation' => Z::string(['required' => 'Observação do Item é obrigatória!'])->optional()
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $pedido = $this->pedidoRepository->findById($dto->id_pedido);

    if (!$pedido)
      throw new ValidationException('Não foi possível cadastrar o Item do Pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id_pedido'
        ]
      ]);

    $produto = $this->produtoRepository->findById($dto->product->id);

    if (!$produto)
      throw new ValidationException('Não foi possível cadastrar o Item do Pedido', [
        [
          'message' => 'Produto não encontrado',
          'origin' => 'id'
        ]
      ]);

    $item = new PedidoItem();

    $item->setPedido($pedido);
    $item->setProduto($produto);
    $item->setValorItem($dto->valor_item);
    $item->setObservacoesItem($dto->observation);

    $this->pedidoItemRepository->create($item);

    return ['message' => 'Item inserido com sucesso ao Pedido!'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Item do Pedido é obrigatório!'])->coerce()->int(),
      'valor_item' => Z::number(['required' => 'Valor do ítem é obrigatório!'])->coerce(),
      'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $itemToUpdate = $this->pedidoItemRepository->findById($dto->id);

    if (!$itemToUpdate) {
      throw new ValidationException('Não foi possível atualizar o Item do Pedido', [
        [
          'message' => 'Item do Pedido não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    //atualizar tudo menos o id do pedido
    $itemToUpdate->setValorItem($dto->valor_item);
    $itemToUpdate->setObservacoesItem($dto->observacoes_item);

    $this->pedidoItemRepository->update($itemToUpdate);

    return ['message' => 'Item atualizado com sucesso'];
  }

  public function delete(array $args)
  {
    $deleteSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Item é obrigatório',
        'invalidType' => 'Id do Item inválido'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $itemToDelete = $this->pedidoItemRepository->findById($dto->id);

    if (!$itemToDelete)
      throw new ValidationException('Não foi possível possível excluir o Item do Pedido', [
        [
          'message' => 'Item não encontrado',
          'origin' => 'id'
        ]
      ]);

    $this->pedidoItemRepository->deleteById($itemToDelete->getId());

    return ['message' => 'Item excluído com sucesso'];
  }
}
