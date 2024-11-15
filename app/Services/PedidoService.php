<?php

namespace App\Services;

use App\Enums\TipoUsuario;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Pedido;
use App\Models\User;
use App\Repositories\IPedidoRepository;
use App\Repositories\IPedidoItemRepository;
use App\Services\PedidoItemService;
use App\Models\PedidoItem;

class PedidoService
{

  public function __construct(
    private readonly IPedidoRepository $pedidoRepository,
    private readonly UserService $userService,
    private readonly IPedidoItemRepository $pedidoItemRepository,
    private readonly PedidoItemService $pedidoItemService
  ) {}

  public function query()
  {
    $pedidos = $this->pedidoRepository->findMany();

    $raw = array_map(function ($pedido) {
      return [
        'id_pedido' => $pedido->getIdPedido(),
        'data_pedido' => $pedido->getDataPedido(),
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

  public function getById(array $args)
  {
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
        'id_pedido' => $pedido->getIdPedido(),
        'data_pedido' => $pedido->getDataPedido(),
        'id_cliente' => $pedido->getCliente(),
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

  public function create(array $args)
  {
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
          'id_item' => Z::string(['required' => 'Id do Item é obrigatório!']),
          'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
          'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
        ])->coerce()
      )
    ])->coerce();

    // {
    //   "itens": [
    //     {
    //       "id_pedido": "",
    //       "id_item": "",
    //       "valor_item": "",
    //       "observacoes_item": "",
    //     },
    //   ]
    // }

    $dto = $createSchema->parseNoSafe($args);

    $clienteArgs = $this->userService->getById($dto->id_cliente);

    if (!$clienteArgs) {
      throw new ValidationException('Não foi possível inserir o Pedido', [
        [
          'message' => 'Cliente não encontrado',
          'origin' => 'cliente'
        ]
      ]);
    }

    $cliente = new User(
      id: $clienteArgs['user']['id'],
      name: $clienteArgs['user']['name'],
      login: $clienteArgs['user']['login'],
      active: $clienteArgs['user']['active'],
      tipo: TipoUsuario::tryFrom($clienteArgs['user']['tipo']),
    );

    $pedido = new Pedido(
      data_pedido: $dto->data_pedido,
      cliente: $cliente,
      valor_total: $dto->valor_total,
      status: $dto->status,
      observacoes: $dto->observacoes,
      forma_pagamento: $dto->forma_pagamento,
      tipo: $dto->tipo,
      endereco_entrega: $dto->endereco_entrega,
      taxa_entrega: $dto->taxa_entrega
    );

    $pedidoCriado = $this->pedidoRepository->create($pedido);

    foreach ($dto->itens as $item) {
      $item->id_pedido = $pedidoCriado->getIdPedido();

      $this->pedidoItemService->create($item);
    }

    return ['message' => 'Pedido inserido com sucesso!'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id_pedido' => Z::string(['required' => 'Id do Pedido é obrigatório!']),
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
          'id_pedido' => Z::string(['required' => 'Id do Pedido é obrigatório!']),
          'id_item' => Z::string(['required' => 'Id do Item é obrigatório!']),
          'valor_item' => Z::string(['required' => 'Valor do ítem é obrigatório!']),
          'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])
        ])->coerce()
      )
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

    $clienteArgs = $this->userService->getById($dto->id_cliente);

    if (!$clienteArgs) {
      throw new ValidationException('Não foi possível atualizar o Pedido', [
        [
          'message' => 'Cliente não encontrado',
          'origin' => 'cliente'
        ]
      ]);
    }

    $cliente = new User(
      id: $clienteArgs['user']['id'],
      name: $clienteArgs['user']['name'],
      login: $clienteArgs['user']['login'],
      active: $clienteArgs['user']['active'],
      tipo: $clienteArgs['user']['tipo'],
    );

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
      $item->id_pedido = $pedidoToUpdate->getIdPedido();

      $this->pedidoItemService->update($item);
    }

    return ['message' => 'Pedido atualizado com sucesso'];
  }

  public function delete(array $args)
  {
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
