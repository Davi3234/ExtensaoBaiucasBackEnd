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
      'id_pedido' => Z::number([
        'required' => 'Id do Pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ]),
      'id_produto' => Z::number([
        'required' => 'Id do Item é obrigatório',
        'invalidType' => 'Id do Item inválido'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $item =  $this->pedidoItemRepository->findById($dto->id_pedido, $dto->id_produto);

    if (!$item)
      throw new ValidationException('Não foi possível encontrar o Item do Pedido', [
        [
          'message' => 'Item não encontrado',
          'origin' => ['id_pedido', 'id_produto']
        ]
      ]);

    return [
      'item' => [
        'id_pedido' => $item->getPedido()->getIdPedido(),
        'id_produto' => $item->getProduto()->getIdProduto(),
        'nome_produto' => $item->getProduto()->getNome(),
        'valor_item' => $item->getValorItem(),
        'observacoes_item' => $item->getObservacoesItem(),
      ]
    ];
  }

  public function create(array $args)
  {
    $createSchema = Z::object([
      'id_produto' => Z::number(['required' => 'Id do Produto é obrigatório!'])->coerce(),
      'id_pedido' => Z::number(['required' => 'Id do Pedido é obrigatório!'])->coerce(),
      'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
      'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
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

    $produto = $this->produtoRepository->findById($dto->id_produto);

    if (!$produto)
      throw new ValidationException('Não foi possível cadastrar o Item do Pedido', [
        [
          'message' => 'Produto não encontrado',
          'origin' => 'id_produto'
        ]
      ]);

    $item = new PedidoItem();

    $item->setPedido($pedido);
    $item->setProduto($produto);
    $item->setValorItem($dto->valor_item);
    $item->setObservacoesItem($dto->observacoes_item);

    $this->pedidoItemRepository->create($item);

    return ['message' => 'Item inserido com sucesso ao Pedido!'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id_pedido' => Z::string(['required' => 'Id do Item do Pedido é obrigatório!']),
      'id_produto' => Z::string(['required' => 'Id do Item do Produto é obrigatório!']),
      'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
      'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $itemToUpdate = $this->pedidoItemRepository->findById($dto->id_pedido, $dto->id_produto);

    if (!$itemToUpdate) {
      throw new ValidationException('Não foi possível atualizar o Item do Pedido', [
        [
          'message' => 'Item do Pedido não encontrado',
          'origin' => ['id_pedido', 'id_produto']
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
      'id_pedido' => Z::number([
        'required' => 'Id do Pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ]),
      'id_produto' => Z::number([
        'required' => 'Id do Item é obrigatório',
        'invalidType' => 'Id do Item inválido'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $itemToDelete = $this->getById([
      'id_pedido' => $dto->id_pedido,
      'id_produto' => $dto->id_produto,
    ])['item'];

    if ($itemToDelete)
      throw new ValidationException('Não foi possível possível excluir o Item do Pedido', [
        [
          'message' => 'Item não encontrado',
          'origin' => 'id_produto'
        ]
      ]);

    $this->pedidoItemRepository->deleteById($dto->id_produto, $dto->id_produto);

    return ['message' => 'Item excluído com sucesso'];
  }
}
