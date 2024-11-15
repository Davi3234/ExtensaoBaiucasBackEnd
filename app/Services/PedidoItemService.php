<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\PedidoItem;
use App\Repositories\IPedidoItemRepository;

class PedidoItemService
{

  public function __construct(
    private readonly IPedidoItemRepository $pedidoItemRepository
  ) {}

  public function query()
  {
    $itens = $this->pedidoItemRepository->findMany();

    $raw = array_map(function ($item) {
      return [
        'id_pedido' => $item->getIdPedido(),
        'id_item' => $item->getIdItem(),
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
      'id_item' => Z::number([
        'required' => 'Id do Item é obrigatório',
        'invalidType' => 'Id do Item inválido'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $item =  $this->pedidoItemRepository->findById($dto->id_item);

    if (!$item)
      throw new ValidationException('Não foi possível encontrar o Item do Pedido', [
        [
          'message' => 'Item não encontrado',
          'origin' => 'id_item'
        ]
      ]);

    return [
      'item' => [
        'id_pedido' => $item->getIdPedido(),
        'id_item' => $item->getIdItem(),
        'valor_item' => $item->getValorItem(),
        'observacoes_item' => $item->getObservacoesItem(),
      ]
    ];
  }

  public function create(array $args)
  {
    $createSchema = Z::object([
      'id_item' => Z::string(['required' => 'Id do Item é obrigatório!']),
      'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
      'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $item = new PedidoItem();

    $item->setIdPedido($dto->id_pedido);
    $item->setIdItem($dto->id_item);
    $item->setValorItem($dto->valor_item);
    $item->setObservacoesItem($dto->observacoes_item);

    $this->pedidoItemRepository->create($item);

    return ['message' => 'Item inserido com sucesso ao Pedido!'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id_pedido' => Z::string(['required' => 'Id do Pedido é obrigatório!']),
      'id_item' => Z::string(['required' => 'Id do Item é obrigatório!']),
      'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
      'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $itemToUpdate = $this->pedidoItemRepository->findById($dto->id_item);

    if (!$itemToUpdate) {
      throw new ValidationException('Não foi possível atualizar o Item do Pedido', [
        [
          'message' => 'Item do Pedido não encontrado',
          'origin' => 'id_item'
        ]
      ]);
    }

    //atualizar tudo menos o id do pedido
    $itemToUpdate->setIdItem($dto->id_item);
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
      'id_item' => Z::number([
        'required' => 'Id do Item é obrigatório',
        'invalidType' => 'Id do Item inválido'
      ])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $itemToDelete = $this->getById($dto->id_item)['item'];

    if ($itemToDelete)
      throw new ValidationException('Não foi possível possível excluir o Item do Pedido', [
        [
          'message' => 'Item não encontrado',
          'origin' => 'id_item'
        ]
      ]);

    $this->pedidoItemRepository->deleteById($dto->id_item);

    return ['message' => 'Item excluído com sucesso'];
  }
}
