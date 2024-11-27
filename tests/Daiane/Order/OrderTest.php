<?php

namespace Tests\Daiane\Order;

use App\Enums\TipoEntrega;
use App\Enums\FormaPagamento;
use App\Enums\StatusPedido;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Services\PedidoService;
use App\Repositories\IPedidoRepository;

class OrderTest extends TestCase
{


  //Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status “Em preparação”
  #[Test]
  public function GetPedidosPorStatus()
  {/*
    // Arrange
    $status = StatusPedido::EM_PREPARO;

    $pedidoRepository = TestCase::createMock(IPedidoRepository::class);

    $pedidoRepository
      ->method('findByStatus')
      ->with($status)
      ->willReturn([
        new Pedido(
          id: 1,
          //data_pedido: '2024-11-25',
          //valor_total: 55,
          status: $status,
          observacoes: '',
          tipo: TipoEntrega::DELIVERY,
          //id_cliente: 2
        ),
        new Pedido(
          id: 2,
          //data_pedido: '2024-11-24',
          // valor_total: 100,
          status: $status,
          observacoes: 'Sem troco',
          //tipo: TipoEntrega::NO_LOCAL,
          // id_cliente: 3
        )
      ]);

    // Act
    // $pedidoService = new PedidoService($pedidoRepository);
    //$pedidos = $pedidoService->getPedidosPorStatus([
    //  'status' => $status
    // ]);


    // Assert
    $pedidosEsperados = [
      [
        'id' => 1,
        'data_pedido' => '2024-11-25',
        'valor_total' => 55,
        'status' => $status,
        'observacoes' => '',
        'tipo' => TipoEntrega::DELIVERY,
        'id_cliente' => 2,
      ],
      [
        'id' => 2,
        'data_pedido' => '2024-11-24',
        'valor_total' => 100,
        'status' => $status,
        'observacoes' => 'Sem troco',
        'tipo' => TipoEntrega::NO_LOCAL,
        'id_cliente' => 3,
      ]
    ];

    // $this->assertEquals($pedidosEsperados, $pedidos);*/
  }
}
