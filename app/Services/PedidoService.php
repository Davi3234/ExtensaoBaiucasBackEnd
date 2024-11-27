<?php

namespace App\Services;

use App\Enums\FormaPagamento;
use App\Enums\TipoEntrega;
use App\Enums\StatusPedido;
use Exception\ValidationException;
use Provider\Zod\Z;
use App\Models\Pedido;
use App\Repositories\IPedidoRepository;
use App\Repositories\IProdutoRepository;
use App\Repositories\IUserRepository;
use App\Services\PedidoItemService;
use App\Services\IPedidoItemRepository;
use App\Repositories\ProdutoRepository;
use App\Models\PedidoItem;

class PedidoService
{

  public function __construct(
    private readonly IPedidoRepository $pedidoRepository,
    private readonly PedidoItemService $pedidoItemService,
    private readonly IUserRepository $userRepository,
    private readonly IProdutoRepository $produtoRepository
  ) {}


  public function query()
  {
    $pedidos = $this->pedidoRepository->findMany();

    $raw = array_map(function ($pedido) {
      $itens = $this->pedidoItemService->findManyByIdPed($pedido->getIdPedido());

      return [
        'id' => $pedido->getIdPedido(),
        'date' => $pedido->getDataPedido(),
        'client' => [
          'id' => $pedido->getCliente()->getId(),
          'name' => $pedido->getCliente()->getName(),
          'login' => $pedido->getCliente()->getLogin(),
          'active' => $pedido->getCliente()->getActive(),
        ],
        'totalPrice' => $pedido->getValorTotal(),
        'state' => $pedido->getStatus(),
        'paymentMethod' => $pedido->getFormaPagamento(),
        'observation' => $pedido->getObservacoes(),
        'tipo' => $pedido->getTipo(),
        'type' => $pedido->getEnderecoEntrega(),
        'address' => $pedido->getTaxaEntrega(),
        'items' => array_map(function ($item) {
          return [
            'id' => $item->getId(),
            'product' => $item->getProduto()->getIdProduto(),
            'price' => $item->getValorItem(),
            'observation' => $item->getObservacoesItem()
          ];
        }, $itens),
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

    $itens = $this->pedidoItemService->findManyByIdPed($dto->id);

    return [
      'order' => [
        'id' => $pedido->getIdPedido(),
        'date' => $pedido->getDataPedido(),
        'client' => [
          'id' => $pedido->getCliente()->getId(),
          'name' => $pedido->getCliente()->getName()
        ],
        'items' => array_map(function ($item) {
          return [
            'id' => $item->getId(),
            'product' => [
              'id' => $item->getProduto()->getIdProduto(),
              'name' => $item->getProduto()->getNome(),
            ],
            'price' => $item->getValorItem(),
            'observation' => $item->getObservacoesItem()
          ];
        }, $itens),
        'totalPrice' => $pedido->getValorTotal(),
        'state' => $pedido->getStatus(),
        'paymentMethod' => $pedido->getFormaPagamento(),
        'observation' => $pedido->getObservacoes(),
        'type' => $pedido->getTipo(),
        'address' => $pedido->getEnderecoEntrega()
      ]
    ];
  }

  public function getPedidosPorStatus(array $args)
  {
    $getSchema = Z::object([
      'statusPedido' => Z::enumNative(StatusPedido::class, ['required' => 'O Status é obrigatório!'])
    ])->coerce();

    $dto = $getSchema->parseNoSafe($args);

    $pedidos =  $this->pedidoRepository->findManyByStatus($dto->statusPedido);


    if (!$pedidos)
      throw new ValidationException('Não foi possível encontrar o Pedido', [
        [
          'message' => 'Pedidos com o status informado não encontrado',
          'origin' => 'status'
        ]
      ]);

    return [
      'orders' => array_map(function ($pedido) {
        return [
          'id' => $pedido->getIdPedido(),
          'data_pedido' => $pedido->getDataPedido(),
          'valor_total' => $pedido->getValorTotal(),
          'status' => $pedido->getStatus(),
          'observacoes' => $pedido->getObservacoes(),
          'tipo' => $pedido->getTipo(),
          'id_cliente' => $pedido->getCliente()->getId()
        ];
      }, $pedidos),
    ];
  }

  public function create(array $args)
  {
    $createSchema = Z::object([
      'id_cliente' => Z::number(['required' => 'Id do cliente é obrigatório!'])
        ->coerce()->int(),
      'data_pedido' => Z::string(['required' => 'Data do pedido é obrigatória!'])
        ->defaultValue(date('Y-m-d')),
      'status' => Z::enumNative(StatusPedido::class, ['required' => 'Status é obrigatório!'])
        ->defaultValue(StatusPedido::EM_PREPARO->value),
      'observacoes' => Z::string()->optional(),
      'forma_pagamento' => Z::enumNative(FormaPagamento::class, ['required' => 'Forma de Pagamento é obrigatória!']),
      'tipo_entrega' => Z::enumNative(TipoEntrega::class, ['required' => 'Tipo do pedido é obrigatório!']),
      'endereco_entrega' => Z::string(['required' => 'Endereço de entrega é obrigatório!'])->optional(),
      'itens' => Z::arrayZod(
        Z::object([
          'product' => Z::object([
            'id' => Z::number(['required' => 'O item é de preenchimento obrigatório'])
          ])->coerce(),
          'observation' => Z::string(['required' => 'Observação do Item é obrigatória!'])->optional()
        ])->coerce()
      )
        ->min(1, 'Precisa de no mínimo 1 item para cadastrar um pedido')
    ])->coerce();

    $dto = $createSchema->parseNoSafe($args);

    $cliente = $this->userRepository->findById($dto->id_cliente);

    if (!$cliente) {
      throw new ValidationException('Não foi possível inserir o Pedido', [
        [
          'message' => 'Cliente não encontrado',
          'origin' => 'id_cliente'
        ]
      ]);
    }

    $valor_total = 0;
    foreach ($dto->itens as &$itemProduto) {
      $produto = $this->produtoRepository->findById($itemProduto->product->id);

      if (!$produto) {
        throw new ValidationException('Não foi possível inserir o Produto', [
          [
            'message' => 'Produto não encontrado',
            'origin' => 'id'
          ]
        ]);
      }

      $valor_total += $produto->getValor();
      $itemProduto->valor_item = $produto->getValor();
    }

    $pedido = new Pedido();

    $pedido->setDataPedido($dto->data_pedido);
    $pedido->setCliente($cliente);
    $pedido->setValorTotal($valor_total);
    $pedido->setStatus(StatusPedido::tryFrom($dto->status));
    $pedido->setObservacoes($dto->observacoes);
    $pedido->setFormaPagamento(FormaPagamento::tryFrom($dto->forma_pagamento));
    $pedido->setTipo(TipoEntrega::tryFrom($dto->tipo_entrega));
    $pedido->setEnderecoEntrega($dto->endereco_entrega);

    $pedidoCriado = $this->pedidoRepository->create($pedido);

    foreach ($dto->itens as $item) {

      $item->id_pedido = $pedidoCriado->getIdPedido();

      $itemPedido = (array)$item;
      $this->pedidoItemService->create($itemPedido);
    }

    return ['message' => 'Pedido inserido com sucesso!'];
  }

  public function update(array $args)
  {
    $updateSchema = Z::object([
      'id' => Z::number(['required' => 'Id do Pedido é obrigatório!'])->coerce()->int(),
      'id_cliente' => Z::number(['required' => 'Id do cliente é obrigatório!'])
        ->coerce()->int(),
      'data_pedido' => Z::string(['required' => 'Data do pedido é obrigatória!'])
        ->defaultValue(date('Y-m-d')),
      'status' => Z::enumNative(StatusPedido::class, ['required' => 'Status é obrigatório!']),
      'observacoes' => Z::string(['required' => 'Observações é obrigatório!']),
      'forma_pagamento' => Z::enumNative(FormaPagamento::class, ['required' => 'Forma de Pagamento é obrigatória!']),
      'tipo_entrega' => Z::enumNative(TipoEntrega::class, ['required' => 'Tipo do pedido é obrigatório!']),
      'endereco_entrega' => Z::string(['required' => 'Endereço de entrega é obrigatório!'])->optional(),
      'taxa_entrega' => Z::number(['required' => 'Taxa de entrega é obrigatória!'])
        ->coerce()->optional(),
      'itens' => Z::arrayZod(
        Z::object([
          'id_produto' => Z::number(['required' => 'Id do Produto é obrigatório!'])
            ->coerce()->int(),
          'valor_item' => Z::number(['required' => 'Valor do Item é obrigatório!']),
          'observacoes_item' => Z::string(['required' => 'Observação do Item é obrigatória!'])->optional()
        ])->coerce()
      )->coerce()
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

    if ($pedidoToUpdate->status === StatusPedido::CANCELADO) {
      throw new ValidationException('Não é possível alterar o status de um pedido cancelado.');
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
    $pedidoToUpdate->setStatus(StatusPedido::tryFrom($dto->status));
    $pedidoToUpdate->setObservacoes($dto->observacoes);
    $pedidoToUpdate->setFormaPagamento(FormaPagamento::tryFrom($dto->forma_pagamento));
    $pedidoToUpdate->setTipo(TipoEntrega::tryFrom($dto->tipo_entrega));
    $pedidoToUpdate->setEnderecoEntrega($dto->endereco_entrega);
    $pedidoToUpdate->setTaxaEntrega($dto->taxa_entrega);

    $this->pedidoRepository->update($pedidoToUpdate);

    foreach ($dto->itens as $item) {

      $item->id_pedido = $pedidoToUpdate->getIdPedido();

      $itemPedido = (array)$item;
      $this->pedidoItemService->update($itemPedido);
    }

    return ['message' => 'Pedido atualizado com sucesso'];
  }

  public function filtrarPedidosPorData($dataInicial, $dataFinal = null)
  {
    // Se for fornecida uma data final, busca pelo intervalo de datas
    if ($dataFinal) {
      $pedidos = $this->pedidoRepository->findByDateRange($dataInicial, $dataFinal);
    } else {
      // Senão, filtra apenas pela data inicial
      $pedidos = $this->pedidoRepository->findByDate($dataInicial);
    }

    if (empty($pedidos)) {
      throw new \Exception('Não existem pedidos para as datas informadas');
    }

    $resultados = [];
    foreach ($pedidos as $pedido) {
      $resultados[] = [
        'id' => $pedido->getIdPedido(),
        'data_pedido' => $pedido->getDataPedido(),
        'cliente' => [
          'id' => $pedido->getCliente()->getId(),
          'nome' => $pedido->getCliente()->getName(),
        ],
        'valorTotalPedido' => $pedido->getValorTotal(),
      ];
    }

    return $resultados;
  }


  public function delete(array $args)
  {
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
