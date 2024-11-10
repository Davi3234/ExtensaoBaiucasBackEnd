<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Pedido;
use App\Repositories\IPedidoRepository;

class PedidoService {

  public function __construct(
    private readonly IPedidoRepository $pedidoRepository
  ) {
  }

  public function query() {
    $pedidos = $this->pedidoRepository->findMany();

    $raw = array_map(function ($pedido) {
      return [
        'id_pedido'         => $pedido->getIdPedido(),
        'data_pedido'       => $pedido->getDataPedido(),
        'id_cliente'        => $pedido->getIdCliente(),
        'valor_total'       => $pedido->getValorTotal(),
        'status'            => $pedido->getStatus(),
        'forma_pagamento'   => $pedido->getFormaPagamento(),
        'observacoes'       => $pedido->getObservacoes(),
        'tipo'              => $pedido->getTipo(),
        'endereco_entrega'  => $pedido->getEnderecoEntrega(),
        'taxa_entrega'      => $pedido->getTaxaEntrega(),
      ];
    }, $pedidos);

    return $raw;
  }

  /**
   * Array de pedido
   * @param array $args
   * @return array
   */

  public function getById(array $args) {
    $getSchema = Z::object([
      'id_pedido' => Z::number([
        'required' => 'Id do pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Pedido inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $pedido =  $this->pedidoRepository->findById($dto->id_pedido);

    if (!$pedido)
      throw new ValidationException('Não foi possível encontrar o Pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id_pedido'
        ]
      ]);

    return [
      'pedido' => [
        'id_pedido'         => $pedido->getIdPedido(),
        'data_pedido'       => $pedido->getDataPedido(),
        'id_cliente'        => $pedido->getIdCliente(),
        'valor_total'       => $pedido->getValorTotal(),
        'status'            => $pedido->getStatus(),
        'forma_pagamento'   => $pedido->getFormaPagamento(),
        'observacoes'       => $pedido->getObservacoes(),
        'tipo'              => $pedido->getTipo(),
        'endereco_entrega'  => $pedido->getEnderecoEntrega(),
        'taxa_entrega'      => $pedido->getTaxaEntrega(),
      ]
    ];
  }

  public function create(array $args) {
    $createSchema = Z::object([
      'id_pedido' => Z::string(['required' => 'Id do Pedido é obrigatório!'])
        ->trim(),
      'id_cliente' => Z::string(['required' => 'Id do cliente é obrigatório!'])
        ->trim()
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $pedido = new Pedido();

    $pedido->setIdPedido($dto->id_pedido);
    $pedido->setDataPedido($dto->data_pedido);
    $pedido->setIdCliente($dto->id_cliente);
    $pedido->setValorTotal($dto->valor_total);
    $pedido->setStatus($dto->status);
    $pedido->setFormaPagamento($dto->data_pedido);
    $pedido->setObservacoes($dto->observacoes);
    $pedido->setTipo($dto->tipo);
    $pedido->setEnderecoEntrega($dto->endereco_entrega);
    $pedido->setTaxaEntrega($dto->taxa_entrega);

    $this->pedidoRepository->create($pedido);

    return ['message' => 'Pedido inserido com sucesso!'];
  }

  public function update(array $args) {
    $updateSchema = Z::object([
      'id_pedido' => Z::number([
        'required' => 'Id do Pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Pedido inválido')
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $pedidoToUpdate = $this->pedidoRepository->findById($dto->id_pedido);

    if (!$pedidoToUpdate) {
      throw new ValidationException('Não foi possível atualizar o Pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id_pedido'
        ]
      ]);
    }

    //Atualizar tudo menos o id do pedido
    $pedidoToUpdate->setDataPedido($dto->data_pedido);
    $pedidoToUpdate->setIdCliente($dto->id_cliente);
    $pedidoToUpdate->setValorTotal($dto->valor_total);
    $pedidoToUpdate->setStatus($dto->status);
    $pedidoToUpdate->setFormaPagamento($dto->data_pedido);
    $pedidoToUpdate->setObservacoes($dto->observacoes);
    $pedidoToUpdate->setTipo($dto->tipo);
    $pedidoToUpdate->setEnderecoEntrega($dto->endereco_entrega);
    $pedidoToUpdate->setTaxaEntrega($dto->taxa_entrega);

    $this->pedidoRepository->update($pedidoToUpdate);

    return ['message' => 'Pedido atualizado com sucesso'];
  }

  public function delete(array $args) {
    $deleteSchema = Z::object([
      'id_pedido' => Z::number([
        'required' => 'Id do Pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Pedido inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $pedidoToDelete = $this->getById($dto->id_pedido)['pedido'];

    if ($pedidoToDelete) {
      throw new ValidationException('Não é possível excluir o pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id_pedido'
        ]
      ]);
    }

    $this->pedidoRepository->deleteById($dto->id_pedido);

    return ['message' => 'Pedido excluído com sucesso'];
  }
}
