<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Pedido;
use App\Repositories\IPedidoItemRepository;

class PedidoItemService {

  public function __construct(
    private readonly IPedidoItemRepository $pedidoItemRepository
  ) {
  }

  public function query() {
    $itens = $this->pedidoItemRepository->findMany();

    $raw = array_map(function ($item) {
      return [
        'id_pedido'         => $item->getIdPedido(),
        'id_item'           => $item->getIdItem(),
        'valor_item'        => $item->getValorItem(),
        'observacoes_item'  => $item->getObservacoesItem(),
      ];
    }, $itens);

    return $raw;
  }

  /**
   * Array de itens
   * @param array $args
   * @return array
   */

  public function getById(array $args) {
    $getSchema = Z::object([
      'id_pedido' => Z::number(['required' => 'Id do Pedido é obrigatório', 'invalidType' => 'Id do Pedido inválido'])
      'id_item' => Z::number(['required' => 'Id do Item é obrigatório', 'invalidType' => 'Id do Item inválido'])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $item =  $this->pedidoItemRepository->findById($dto->id_item);

    if (!$item)
      throw new ValidationException('Item não encontrado');

    return [
      'item' => [
       'id_pedido'         => $item->getIdPedido(),
       'id_item'           => $item->getIdItem(),
       'valor_item'        => $item->getValorItem(),
       'observacoes_item'  => $item->getObservacoesItem(),
      ]
    ];
    }

    public function create(array $args) {
        $createSchema = Z::object([
          'id_pedido' => Z::string(['required' => 'Id do Pedido é obrigatório!'])
            ->trim()
          'id_item' => Z::string(['required' => 'Id do Item é obrigatório!'])
            ->trim()
        ])->coerce();
    
        $dto = $createSchema->parseNoSafe($args);
    
        $ItemToCreate = $this->pedidoItemRepository->create($dto->create);
    
        if ($ItemToCreate) {
          throw new ValidationException(
            'Não é possível inserir o item ao pedido',
          );
        }

        $item = new PedidoItem();

        $item->setIdPedido($dto->id_pedido);
        $item->setIdItem($dto->id_item);
        $item->setValorItem($dto->valor_item);
        $item->setObservacoesItem($dto->observacoes_item);

        $this->produtoItemRepository->create($item);

        return ['message' => 'Item inserido com sucesso ao Pedido!'];
    }

    public function update(array $args) {
    $updateSchema = Z::object([
      'id_pedido' => Z::number(['required' => 'Id do Pedido é obrigatório', 'invalidType' => 'Id do Pedido inválido'])
      'id_item' => Z::number(['required' => 'Id do Pedido é obrigatório', 'invalidType' => 'Id do Item inválido'])
        ->coerce()
        ->int()
        ->trim()
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $itemToUpdate = $this->pedidoItemRepository->findById($dto->id_item);

    if (!$itemToUpdate) {
      throw new ValidationException(
        'Não é possível atualizar o item do pedido!',
      );
    }

    //atualizar tudo menos o id do pedido
    $item->setIdItem($dto->id_item);
    $item->setValorItem($dto->valor_item);
    $item->setObservacoesItem($dto->observacoes_item);

    $this->pedidoItemRepository->update($item);

    return ['message' => 'Item atualizado com sucesso'];
    }

    public function delete(array $args) {
    $deleteSchema = Z::object([
      'id_pedido' => Z::number(['required' => 'Id do Pedido é obrigatório', 'invalidType' => 'Id do Pedido inválido'])
      'id_item' => Z::number(['required' => 'Id do Item é obrigatório', 'invalidType' => 'Id do Item inválido'])
        ->coerce()
        ->int()
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $itemToDelete = $this->getById($dto->id_item)['item'];

    if ($itemToDelete) {
      throw new ValidationException(
        'Não é possível excluir o item',
        [
          ['message' => 'Item não encontrado', 'cause' => 'id_item']
        ]
      );
    }

    $this->ProdutoItemRepository->deleteById($dto->id_item);

    return ['message' => 'Item excluído com sucesso'];
  }
}
