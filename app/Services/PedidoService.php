<?php

namespace App\Services;

use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Pedido;
use App\Repositories\IPedidoRepository;
use App\Repositories\IUserRepository;
use App\Services\PedidoItemService;

class PedidoService {

  public function __construct(
    private readonly IPedidoRepository $pedidoRepository,
    private readonly PedidoItemService $pedidoItemService,
    private readonly IUserRepository $userRepository
  ) {
  }

  public function query() {
    $pedidos = $this->pedidoRepository->findMany();

    $raw = array_map(function ($pedido) {
      return [
        'id' => $pedido->getIdPedido(),
        'data_pedido' => $pedido->getDataPedido(),
        'id_cliente' => $pedido->getCliente(),
        'cliente' => [
          'nome' => $pedido->getCliente()->getName(),
        ],
        'valor_total' => $pedido->getValorTotal(),
        'status' => $pedido->getStatus(),
        'forma_pagamento' => $pedido->getFormaPagamento(),
        'observacoes' => $pedido->getObservacoes(),
        'tipo' => $pedido->getTipo(),
        'endereco_entrega' => $pedido->getEnderecoEntrega(),
        'taxa_entrega' => $pedido->getTaxaEntrega(),
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
      'id' => Z::number([
        'required' => 'Id do pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Pedido inválido')
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $pedido =  $this->pedidoRepository->findById($dto->id);

    if (!$pedido)
      throw new ValidationException('Não foi possível encontrar o Pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id'
        ]
      ]);

    return [
      'pedido' => [
        'id' => $pedido->getIdPedido(),
        'data_pedido' => $pedido->getDataPedido(),
        'id_cliente' => $pedido->getCliente(),
        'cliente' => [
          'nome' => $pedido->getCliente()->getName(),
        ],
        'valor_total' => $pedido->getValorTotal(),
        'status' => $pedido->getStatus(),
        'forma_pagamento' => $pedido->getFormaPagamento(),
        'observacoes' => $pedido->getObservacoes(),
        'tipo' => $pedido->getTipo(),
        'endereco_entrega' => $pedido->getEnderecoEntrega(),
        'taxa_entrega' => $pedido->getTaxaEntrega(),
      ]
    ];
  }

  public function create(array $args) {
    $createSchema = Z::object([
      'id_cliente' => Z::string(['required' => 'Id do cliente é obrigatório!']),
      'data_pedido' => Z::string(['required' => 'Data do pedido é obrigatória!']),
      'valor_total' => Z::string(['required' => 'Valor total é obrigatório!']),
      'status' => Z::string(['required' => 'Status é obrigatório!']),
      'observacoes' => Z::string(['required' => 'Observações é obrigatório!']),
      'forma_pagamento' => Z::string(['required' => 'Forma de Pagamento é obrigatória!']),
      'tipo' => Z::string(['required' => 'Tipo do pedido é obrigatório!']),
      'endereco_entrega' => Z::string(['required' => 'Endereço de entrega é obrigatório!']),
      'taxa_entrega' => Z::string(['required' => 'Taxa de entrega é obrigatória!']),
      //Colocando itens
      'itens' => Z::arrayZod(
        Z::object([
          'id_produto' => Z::string(['required' => 'Id do Produto é obrigatório!']),
          'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
          'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
        ])->coerce()
      )
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $cliente = $this->userRepository->findById($dto->id_cliente);

    if (!$cliente) {
      throw new ValidationException('Não foi possível inserir o Pedido', [
        [
          'message' => 'Cliente não encontrado',
          'origin' => 'cliente'
        ]
      ]);
    }

    $pedido = new Pedido(
      dataPedido: $dto->data_pedido,
      cliente: $cliente,
      valorTotal: $dto->valor_total,
      status: $dto->status,
      observacoes: $dto->observacoes,
      formaPagamento: $dto->forma_pagamento,
      tipo: $dto->tipo,
      enderecoEntrega: $dto->endereco_entrega,
      taxaEntrega: $dto->taxa_entrega
    );

    $pedidoCriado = $this->pedidoRepository->create($pedido);

    foreach ($dto->itens as $item) {
      $item->id = $pedidoCriado->getIdPedido();

      $this->pedidoItemService->create($item);
    }

    return ['message' => 'Pedido inserido com sucesso!'];
  }

  public function update(array $args) {
    $updateSchema = Z::object([
      'id' => Z::string(['required' => 'Id do Pedido é obrigatório!']),
      'id_cliente' => Z::string(['required' => 'Id do cliente é obrigatório!']),
      'data_pedido' => Z::string(['required' => 'Data do pedido é obrigatória!']),
      'valor_total' => Z::string(['required' => 'Valor total é obrigatório!']),
      'status' => Z::string(['required' => 'Status é obrigatório!']),
      'observacoes' => Z::string(['required' => 'Observações é obrigatório!']),
      'forma_pagamento' => Z::string(['required' => 'Forma de Pagamento é obrigatória!']),
      'tipo' => Z::string(['required' => 'Tipo do pedido é obrigatório!']),
      'endereco_entrega' => Z::string(['required' => 'Endereço de entrega é obrigatório!']),
      'taxa_entrega' => Z::string(['required' => 'Taxa de entrega é obrigatória!']),
      'itens' => Z::arrayZod(
        Z::object([
          'id' => Z::string(['required' => 'Id do Pedido é obrigatório!']),
          'id_produto' => Z::string(['required' => 'Id do Produto é obrigatório!']),
          'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
          'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
        ])->coerce()
      )
    ])->coerce();

    $dto = $updateSchema->parseNoSafe($args);

    $pedidoToUpdate = $this->pedidoRepository->findById($dto->id);

    if (!$pedidoToUpdate) {
      throw new ValidationException('Não foi possível atualizar o Pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    $cliente = $this->userRepository->findById($dto->id_cliente);

    if (!$cliente) {
      throw new ValidationException('Não foi possível atualizar o Pedido', [
        [
          'message' => 'Cliente não encontrado',
          'origin' => 'cliente'
        ]
      ]);
    }

    $pedidoToUpdate->setDataPedido($dto->data_pedido);
    $pedidoToUpdate->setCliente($cliente);
    $pedidoToUpdate->setValorTotal($dto->valor_total);
    $pedidoToUpdate->setStatus($dto->status);
    $pedidoToUpdate->setFormaPagamento($dto->data_pedido);
    $pedidoToUpdate->setObservacoes($dto->observacoes);
    $pedidoToUpdate->setTipo($dto->tipo);
    $pedidoToUpdate->setEnderecoEntrega($dto->endereco_entrega);
    $pedidoToUpdate->setTaxaEntrega($dto->taxa_entrega);

    $this->pedidoRepository->update($pedidoToUpdate);

    foreach ($dto->itens as $item) {
      $item->id = $pedidoToUpdate->getIdPedido();

      $this->pedidoItemService->update($item);
    }

    return ['message' => 'Pedido atualizado com sucesso'];
  }

  public function delete(array $args) {
    $deleteSchema = Z::object([
      'id' => Z::number([
        'required' => 'Id do Pedido é obrigatório',
        'invalidType' => 'Id do Pedido inválido'
      ])
        ->coerce()
        ->int()
        ->gt(0, 'Id do Pedido inválido')
    ])->coerce();

    $dto = $deleteSchema->parseNoSafe($args);

    $pedidoToDelete = $this->pedidoRepository->findById($dto->id);

    if (!$pedidoToDelete) {
      throw new ValidationException('Não é possível excluir o pedido', [
        [
          'message' => 'Pedido não encontrado',
          'origin' => 'id'
        ]
      ]);
    }

    $this->pedidoRepository->deleteById($pedidoToDelete->getIdPedido());

    return ['message' => 'Pedido excluído com sucesso'];
  }
}
