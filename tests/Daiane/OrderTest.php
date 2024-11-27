<?php

namespace Tests\Order;

use App\Enums\FormaPagamento;
use App\Enums\StatusPedido;
use App\Enums\TipoEntrega;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Produto;
use App\Models\PedidoItem;
use App\Services\PedidoService;
use App\Repositories\IPedidoRepository;
use App\Repositories\IUserRepository;
use App\Repositories\IProdutoRepository;
use App\Services\PedidoItemService;
use Exception\ValidationException;

class OrderTest extends TestCase
{

  //Caso de teste 01: Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status “Em preparo”
  #[Test]
  public function GetPedidosPorStatusEmPreparo()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $pedidoRepositoryMock
      ->method('findManyByStatus')
      ->with(StatusPedido::EM_PREPARO->value)
      ->willReturn([
        new Pedido(
          id: 1,
          status: StatusPedido::EM_PREPARO,
          observacoes: '',
          tipo: TipoEntrega::DELIVERY,
          cliente: $userMock,
          dataPedido: '2024-10-10',
          valorTotal: 1040
        ),
        new Pedido(
          id: 2,
          status: StatusPedido::EM_PREPARO,
          observacoes: 'Sem troco',
          tipo: TipoEntrega::NO_LOCAL,
          cliente: $userMock,
          dataPedido: '2024-07-14',
          valorTotal: 101.5
        )
      ]);

    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);
    $produtoRepositoryMock = $this->createMock(IProdutoRepository::class);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $userRepositoryMock,
      $produtoRepositoryMock
    );

    // Act
    $pedidosRetornados = $pedidoService->getPedidosPorStatus([
      'statusPedido' => StatusPedido::EM_PREPARO->value
    ]);

    $pedidosEsperados = [
      'orders' => [
        [
          'id' => 1,
          'status' => StatusPedido::EM_PREPARO,
          'observacoes' => '',
          'tipo' => TipoEntrega::DELIVERY,
          'id_cliente' => 1,
          'data_pedido' => '2024-10-10',
          'valor_total' => 1040
        ],
        [
          'id' => 2,
          'status' => StatusPedido::EM_PREPARO,
          'observacoes' => 'Sem troco',
          'tipo' => TipoEntrega::NO_LOCAL,
          'id_cliente' => 1,
          'data_pedido' => '2024-07-14',
          'valor_total' => 101.5
        ]
      ]
    ];

    //Assert
    $this->assertEquals(
      $pedidosEsperados,
      $pedidosRetornados,
      'Os pedidos retornados não correspondem aos pedidos esperados.'
    );
    echo "Teste finalizado com sucesso! Todos os pedidos estão corretos de acordo com o status EM PREPARO.";
  }

  //Caso de teste 02: Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status “Cancelado”
  #[Test]
  public function GetPedidosPorStatusCancelado()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $pedidoRepositoryMock
      ->method('findManyByStatus')
      ->with(StatusPedido::CANCELADO->value)
      ->willReturn([
        new Pedido(
          id: 1,
          status: StatusPedido::CANCELADO,
          observacoes: 'Cancelado por demora na entrega',
          tipo: TipoEntrega::DELIVERY,
          cliente: $userMock,
          dataPedido: '2024-10-26',
          valorTotal: 2345
        ),
        new Pedido(
          id: 2,
          status: StatusPedido::CANCELADO,
          observacoes: '',
          tipo: TipoEntrega::NO_LOCAL,
          cliente: $userMock,
          dataPedido: '2024-11-27',
          valorTotal: 456
        )
      ]);

    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);
    $produtoRepositoryMock = $this->createMock(IProdutoRepository::class);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $userRepositoryMock,
      $produtoRepositoryMock
    );

    // Act
    $pedidosRetornados = $pedidoService->getPedidosPorStatus([
      'statusPedido' => StatusPedido::CANCELADO->value
    ]);

    $pedidosEsperados = [
      'orders' => [
        [
          'id' => 1,
          'status' => StatusPedido::CANCELADO,
          'observacoes' => 'Cancelado por demora na entrega',
          'tipo' => TipoEntrega::DELIVERY,
          'id_cliente' => 1,
          'data_pedido' => '2024-10-26',
          'valor_total' => 2345
        ],
        [
          'id' => 2,
          'status' => StatusPedido::CANCELADO,
          'observacoes' => '',
          'tipo' => TipoEntrega::NO_LOCAL,
          'id_cliente' => 1,
          'data_pedido' => '2024-11-27',
          'valor_total' => 456
        ]
      ]
    ];

    //Assert
    $this->assertEquals(
      $pedidosEsperados,
      $pedidosRetornados,
      'Os pedidos retornados não correspondem aos pedidos esperados.'
    );
    echo "Teste finalizado com sucesso! Todos os pedidos estão corretos de acordo com o status CANCELADO.";
  }

  //Caso de teste 03: Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status Finalizado
  #[Test]
  public function GetPedidosPorStatusFinalizado()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $pedidoRepositoryMock
      ->method('findManyByStatus')
      ->with(StatusPedido::FINALIZADO->value)
      ->willReturn([
        new Pedido(
          id: 1,
          status: StatusPedido::FINALIZADO,
          observacoes: 'Entrega realizada com sucesso!',
          tipo: TipoEntrega::DELIVERY,
          cliente: $userMock,
          dataPedido: '2024-08-10',
          valorTotal: 35
        ),
        new Pedido(
          id: 2,
          status: StatusPedido::FINALIZADO,
          observacoes: 'Com gorjeta',
          tipo: TipoEntrega::NO_LOCAL,
          cliente: $userMock,
          dataPedido: '2024-05-05',
          valorTotal: 890
        )
      ]);

    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);
    $produtoRepositoryMock = $this->createMock(IProdutoRepository::class);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $userRepositoryMock,
      $produtoRepositoryMock
    );

    // Act
    $pedidosRetornados = $pedidoService->getPedidosPorStatus([
      'statusPedido' => StatusPedido::FINALIZADO->value
    ]);

    $pedidosEsperados = [
      'orders' => [
        [
          'id' => 1,
          'status' => StatusPedido::FINALIZADO,
          'observacoes' => 'Entrega realizada com sucesso!',
          'tipo' => TipoEntrega::DELIVERY,
          'id_cliente' => 1,
          'data_pedido' => '2024-08-10',
          'valor_total' => 35
        ],
        [
          'id' => 2,
          'status' => StatusPedido::FINALIZADO,
          'observacoes' => 'Com gorjeta',
          'tipo' => TipoEntrega::NO_LOCAL,
          'id_cliente' => 1,
          'data_pedido' => '2024-05-05',
          'valor_total' => 890
        ]
      ]
    ];

    //Assert
    $this->assertEquals(
      $pedidosEsperados,
      $pedidosRetornados,
      'Os pedidos retornados não correspondem aos pedidos esperados.'
    );
    echo "Teste finalizado com sucesso! Todos os pedidos estão corretos de acordo com o status FINALIZADO.";
  }

  //Caso de teste 04: Verificar se o sistema exibe corretamente os itens presentes em um pedido que contém apenas um item
  #[Test]
  public function testGetByIdRetornaPedidoComUmItem()
  {
    // Arrange
    $pedidoId = 1;

    $produtoMock = $this->createMock(Produto::class);
    $produtoMock->method('getIdProduto')->willReturn(101);
    $produtoMock->method('getNome')->willReturn('X BACON');

    $itemMock = $this->createMock(PedidoItem::class);
    $itemMock->method('getId')->willReturn(1);
    $itemMock->method('getProduto')->willReturn($produtoMock);
    $itemMock->method('getValorItem')->willReturn(100.00);
    $itemMock->method('getObservacoesItem')->willReturn('Com bastante bacon');

    $clienteMock = $this->createMock(User::class);
    $clienteMock->method('getId')->willReturn(1);
    $clienteMock->method('getName')->willReturn('Cliente Teste');

    $pedidoMock = $this->createMock(Pedido::class);
    $pedidoMock->method('getIdPedido')->willReturn($pedidoId);
    $pedidoMock->method('getDataPedido')->willReturn('2024-11-27');
    $pedidoMock->method('getCliente')->willReturn($clienteMock);
    $pedidoMock->method('getValorTotal')->willReturn(100.00);
    $pedidoMock->method('getStatus')->willReturn(StatusPedido::EM_PREPARO);
    $pedidoMock->method('getFormaPagamento')->willReturn(FormaPagamento::CARTAO);
    $pedidoMock->method('getObservacoes')->willReturn('Sem troco');
    $pedidoMock->method('getTipo')->willReturn(TipoEntrega::DELIVERY);
    $pedidoMock->method('getEnderecoEntrega')->willReturn('Rua 123, Bairro Centro, Apiúna');

    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoRepositoryMock->method('findById')
      ->with($pedidoId)
      ->willReturn($pedidoMock);

    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $pedidoItemServiceMock->method('findManyByIdPed')
      ->with($pedidoId)
      ->willReturn([$itemMock]);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $this->createMock(IUserRepository::class),
      $this->createMock(IProdutoRepository::class)
    );

    // Act
    $resultado = $pedidoService->getById(['id' => $pedidoId]);

    // Assert
    $esperado = [
      'order' => [
        'id' => 1,
        'date' => '2024-11-27',
        'client' => [
          'id' => 1,
          'name' => 'Cliente Teste',
        ],
        'items' => [
          [
            'id' => 1,
            'product' => [
              'id' => 101,
              'name' => 'X BACON',
            ],
            'price' => 100.00,
            'observation' => 'Com bastante bacon',
          ],
        ],
        'totalPrice' => 100.00,
        'state' => StatusPedido::EM_PREPARO,
        'paymentMethod' => FormaPagamento::CARTAO,
        'observation' => 'Sem troco',
        'type' => TipoEntrega::DELIVERY,
        'address' => 'Rua 123, Bairro Centro, Apiúna',
      ],
    ];

    $this->assertEquals($esperado, $resultado, 'Os dados do pedido retornados não estão corretos.');
  }

  //Caso de teste 05: Verificar se o sistema exibe corretamente os itens presentes em um pedido que contém três itens
  #[Test]
  public function testExibirItensPedidoComTresItens()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $userMock->method('getName')->willReturn('Iranice Da Silva');

    $pedidoMock = $this->createMock(Pedido::class);
    $pedidoMock->method('getIdPedido')->willReturn(1);
    $pedidoMock->method('getDataPedido')->willReturn('2024-11-27');
    $pedidoMock->method('getCliente')->willReturn($userMock);
    $pedidoMock->method('getValorTotal')->willReturn(90.0);
    $pedidoMock->method('getStatus')->willReturn(StatusPedido::EM_PREPARO);
    $pedidoMock->method('getFormaPagamento')->willReturn(FormaPagamento::CARTAO);
    $pedidoMock->method('getObservacoes')->willReturn('No capricho');
    $pedidoMock->method('getTipo')->willReturn(TipoEntrega::DELIVERY);
    $pedidoMock->method('getEnderecoEntrega')->willReturn('Rua José Peters, 435, Centro, Apiúna');

    //Mock dos produtos
    $produtoMock1 = $this->createMock(Produto::class);
    $produtoMock1->method('getIdProduto')->willReturn(1);
    $produtoMock1->method('getNome')->willReturn("Batata com Bacon");

    $produtoMock2 = $this->createMock(Produto::class);
    $produtoMock2->method('getIdProduto')->willReturn(2);
    $produtoMock2->method('getNome')->willReturn("Refrigerante Coca-Cola");

    $produtoMock3 = $this->createMock(Produto::class);
    $produtoMock3->method('getIdProduto')->willReturn(3);
    $produtoMock3->method('getNome')->willReturn("X-burguer");

    //Mock dos itens
    $itemMock1 = $this->createMock(PedidoItem::class);
    $itemMock1->method('getId')->willReturn(1);
    $itemMock1->method('getProduto')->willReturn($produtoMock1);
    $itemMock1->method('getValorItem')->willReturn(45.0);
    $itemMock1->method('getObservacoesItem')->willReturn("Com Pouco Bacon");

    $itemMock2 = $this->createMock(PedidoItem::class);
    $itemMock2->method('getId')->willReturn(2);
    $itemMock2->method('getProduto')->willReturn($produtoMock2);
    $itemMock2->method('getValorItem')->willReturn(15.0);
    $itemMock2->method('getObservacoesItem')->willReturn("Copo com limão e gelo");

    $itemMock3 = $this->createMock(PedidoItem::class);
    $itemMock3->method('getId')->willReturn(3);
    $itemMock3->method('getProduto')->willReturn($produtoMock3);
    $itemMock3->method('getValorItem')->willReturn(30.0);
    $itemMock3->method('getObservacoesItem')->willReturn("Com dois hamburguers");

    $pedidoItemServiceMock
      ->method('findManyByIdPed')
      ->with(1)
      ->willReturn([$itemMock1, $itemMock2, $itemMock3]);

    $pedidoRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($pedidoMock);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $this->createMock(IUserRepository::class),
      $this->createMock(IProdutoRepository::class)
    );

    // Act
    $resultado = $pedidoService->getById(['id' => 1]);

    // Resultado esperado
    $resultadoEsperado = [
      'order' => [
        'id' => 1,
        'date' => '2024-11-27',
        'client' => [
          'id' => 1,
          'name' => 'Iranice Da Silva',
        ],
        'items' => [
          [
            'id' => 1,
            'product' => [
              'id' => 1,
              'name' => 'Batata com Bacon',
            ],
            'price' => 45,
            'observation' => 'Com Pouco Bacon',
          ],
          [
            'id' => 2,
            'product' => [
              'id' => 2,
              'name' => 'Refrigerante Coca-Cola',
            ],
            'price' => 15,
            'observation' => 'Copo com limão e gelo',
          ],
          [
            'id' => 3,
            'product' => [
              'id' => 3,
              'name' => 'X-burguer',
            ],
            'price' => 30,
            'observation' => 'Com dois hamburguers',
          ],
        ],
        'totalPrice' => 90,
        'state' => StatusPedido::EM_PREPARO,
        'paymentMethod' => FormaPagamento::CARTAO,
        'observation' => 'No capricho',
        'type' => TipoEntrega::DELIVERY,
        'address' => 'Rua José Peters, 435, Centro, Apiúna',
      ],
    ];

    // Assert
    $this->assertEquals($resultadoEsperado, $resultado);
  }

  //Caso de teste 06: Verificar se o filtro de pedidos por data está funcionando corretamente trazendo somente os pedidos da data informada
  #[Test]
  public function testFiltrarPedidosPorData()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $userMock->method('getName')->willReturn('Iranice Da Silva');

    $pedidoMock1 = $this->createMock(Pedido::class);
    $pedidoMock1->method('getIdPedido')->willReturn(1);
    $pedidoMock1->method('getDataPedido')->willReturn('2024-11-27');
    $pedidoMock1->method('getCliente')->willReturn($userMock);
    $pedidoMock1->method('getValorTotal')->willReturn(100.0);

    $pedidoMock2 = $this->createMock(Pedido::class);
    $pedidoMock2->method('getIdPedido')->willReturn(2);
    $pedidoMock2->method('getDataPedido')->willReturn('2024-11-28');
    $pedidoMock2->method('getCliente')->willReturn($userMock);
    $pedidoMock2->method('getValorTotal')->willReturn(50.0);

    $pedidoMock3 = $this->createMock(Pedido::class);
    $pedidoMock3->method('getIdPedido')->willReturn(3);
    $pedidoMock3->method('getDataPedido')->willReturn('2024-11-27');
    $pedidoMock3->method('getCliente')->willReturn($userMock);
    $pedidoMock3->method('getValorTotal')->willReturn(150.0);

    $pedidoRepositoryMock
      ->method('findByDate')
      ->with('2024-11-27')
      ->willReturn([$pedidoMock1, $pedidoMock3]);

    $pedidoItemServiceMock->method('findManyByIdPed')->willReturn([]);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $this->createMock(IUserRepository::class),
      $this->createMock(IProdutoRepository::class)
    );

    // Act
    $resultados = $pedidoService->filtrarPedidosPorData('2024-11-27');

    $resultadoEsperado = [
      [
        'id' => 1,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Iranice Da Silva',
        ],
        'valorTotalPedido' => 100.0,
      ],
      [
        'id' => 3,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Iranice Da Silva',
        ],
        'valorTotalPedido' => 150.0,
      ]
    ];

    // Assert
    $this->assertEquals($resultadoEsperado, $resultados);
  }

  //Caso de teste 07: Verificar se o filtro de pedidos por data está funcionando corretamente trazendo somente os pedidos informados no intervalo de datas
  #[Test]
  public function testFiltrarPedidosPorDatas()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $userMock->method('getName')->willReturn('Iranice Da Silva');

    $pedidoMock1 = $this->createMock(Pedido::class);
    $pedidoMock1->method('getIdPedido')->willReturn(1);
    $pedidoMock1->method('getDataPedido')->willReturn('2024-11-27');
    $pedidoMock1->method('getCliente')->willReturn($userMock);
    $pedidoMock1->method('getValorTotal')->willReturn(100.0);

    $pedidoMock2 = $this->createMock(Pedido::class);
    $pedidoMock2->method('getIdPedido')->willReturn(2);
    $pedidoMock2->method('getDataPedido')->willReturn('2024-11-28');
    $pedidoMock2->method('getCliente')->willReturn($userMock);
    $pedidoMock2->method('getValorTotal')->willReturn(50.0);

    $pedidoMock3 = $this->createMock(Pedido::class);
    $pedidoMock3->method('getIdPedido')->willReturn(3);
    $pedidoMock3->method('getDataPedido')->willReturn('2024-11-27');
    $pedidoMock3->method('getCliente')->willReturn($userMock);
    $pedidoMock3->method('getValorTotal')->willReturn(150.0);

    $pedidoRepositoryMock
      ->method('findByDateRange')
      ->with('2024-11-27', '2024-11-28')
      ->willReturn([$pedidoMock1, $pedidoMock2, $pedidoMock3]);

    $pedidoItemServiceMock->method('findManyByIdPed')->willReturn([]);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $this->createMock(IUserRepository::class),
      $this->createMock(IProdutoRepository::class)
    );

    // Act
    $resultados = $pedidoService->filtrarPedidosPorData('2024-11-27', '2024-11-28');

    $resultadoEsperado = [
      [
        'id' => 1,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Iranice Da Silva',
        ],
        'valorTotalPedido' => 100.0,
      ],
      [
        'id' => 2,
        'data_pedido' => '2024-11-28',
        'cliente' => [
          'id' => 1,
          'nome' => 'Iranice Da Silva',
        ],
        'valorTotalPedido' => 50.0,
      ],
      [
        'id' => 3,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Iranice Da Silva',
        ],
        'valorTotalPedido' => 150.0,
      ]
    ];

    // Assert
    $this->assertEquals($resultadoEsperado, $resultados);
  }

  //Caso de teste 08: Verificar se o filtro de pedidos por data está funcionando corretamente mostrando um erro ao informar uma data que não possuem pedidos registrados
  #[Test]
  public function testFiltrarPedidosPorDataQueNaoPossuaPedidos()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $userMock->method('getName')->willReturn('Iranice Da Silva');

    $pedidoRepositoryMock
      ->method('findByDateRange')
      ->with('2024-11-29', '2024-11-30')
      ->willReturn([]);

    $pedidoItemServiceMock->method('findManyByIdPed')->willReturn([]);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $this->createMock(IUserRepository::class),
      $this->createMock(IProdutoRepository::class)
    );

    //Assert
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Não existem pedidos para as datas informadas');

    // Act
    $pedidoService->filtrarPedidosPorData('2024-11-29', '2024-11-30');
  }

  //Caso de teste 09: Verificar se a atualização do status ocorre corretamente quando o pedido está com status "Em Preparo" e é alterado para "Finalizado"
  #[Test]
  public function UpdateStatusPedidoParaFinalizado()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);

    $pedidoMock = new Pedido(
      id: 1,
      dataPedido: '2024-11-15',
      cliente: null,
      valorTotal: 100.00,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::PIX,
      observacoes: 'Pedido em andamento',
      tipo: TipoEntrega::NO_LOCAL,
      enderecoEntrega: 'Rua Exemplo, 123',
      taxaEntrega: 10.00
    );

    $pedidoRepositoryMock->expects($this->once())
      ->method('findById')
      ->with(1)
      ->willReturn($pedidoMock);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $userMock->method('getName')->willReturn('Daiane');

    $userRepositoryMock->expects($this->once())
      ->method('findById')
      ->with(1)
      ->willReturn($userMock);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $userRepositoryMock,
      $this->createMock(IProdutoRepository::class)
    );

    $args = [
      'id' => 1,
      'id_cliente' => 1,
      'data_pedido' => '2024-11-15',
      'status' => StatusPedido::FINALIZADO->value,
      'observacoes' => 'Pedido finalizado',
      'forma_pagamento' => FormaPagamento::CARTAO->value,
      'tipo_entrega' => TipoEntrega::NO_LOCAL->value,
      'endereco_entrega' => 'Bairro Centro, Rua José Peters, 175',
      'taxa_entrega' => 10.00,

    ];

    // Act
    $resultados = $pedidoService->update($args);

    //Assert
    $this->assertEquals(StatusPedido::FINALIZADO, $pedidoMock->status);
    $this->assertEquals(['message' => 'Pedido atualizado com sucesso'], $resultados);
  }

  //Caso de teste 10: Verificar se a tentativa de atualizar o status de um pedido com status "Cancelado" para novo status "Em Preparo" resulta em erro
  #[Test]
  public function UpdateStatusPedido_CanceladoParaEmPreparo_DeveRetornarErro()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);

    $pedidoMock = new Pedido(
      id: 1,
      dataPedido: '2024-11-15',
      cliente: null,
      valorTotal: 100.00,
      status: StatusPedido::CANCELADO,
      formaPagamento: FormaPagamento::PIX,
      observacoes: 'Pedido cancelado pelo cliente',
      tipo: TipoEntrega::NO_LOCAL,
      enderecoEntrega: 'Rua Exemplo, 123',
      taxaEntrega: 10.00
    );

    $pedidoRepositoryMock->expects($this->once())
      ->method('findById')
      ->with(1)
      ->willReturn($pedidoMock);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $userRepositoryMock,
      $this->createMock(IProdutoRepository::class)
    );

    $args = [
      'id' => 1,
      'id_cliente' => 1,
      'data_pedido' => '2024-11-15',
      'status' => StatusPedido::EM_PREPARO->value,
      'observacoes' => 'Preparando o pedido',
      'forma_pagamento' => FormaPagamento::PIX->value,
      'tipo_entrega' => TipoEntrega::NO_LOCAL->value,
      'endereco_entrega' => 'Rua Exemplo, 123',
      'taxa_entrega' => 10.00,
    ];

    // Act & Assert
    $this->expectException(ValidationException::class);
    $this->expectExceptionMessage('Não é possível alterar o status de um pedido cancelado.');

    $pedidoService->update($args);
  }

  //Caso de teste 11: Verificar se a atualização de status de um pedido com status "Em Preparo" para "Cancelado" é realizada corretamente
  #[Test]
  public function UpdateStatusPedidoParaCancelado()
  {
    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);

    $pedidoMock = new Pedido(
      id: 1,
      dataPedido: '2024-11-15',
      cliente: null,
      valorTotal: 100.00,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::PIX,
      observacoes: 'Pedido em andamento',
      tipo: TipoEntrega::NO_LOCAL,
      enderecoEntrega: 'Rua Exemplo, 123',
      taxaEntrega: 10.00
    );

    $pedidoRepositoryMock->expects($this->once())
      ->method('findById')
      ->with(1)
      ->willReturn($pedidoMock);

    $userMock = $this->createMock(User::class);
    $userMock->method('getId')->willReturn(1);
    $userMock->method('getName')->willReturn('Daiane');

    $userRepositoryMock->expects($this->once())
      ->method('findById')
      ->with(1)
      ->willReturn($userMock);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $userRepositoryMock,
      $this->createMock(IProdutoRepository::class)
    );

    $args = [
      'id' => 1,
      'id_cliente' => 1,
      'data_pedido' => '2024-11-15',
      'status' => StatusPedido::CANCELADO->value,
      'observacoes' => 'Pedido finalizado',
      'forma_pagamento' => FormaPagamento::CARTAO->value,
      'tipo_entrega' => TipoEntrega::NO_LOCAL->value,
      'endereco_entrega' => 'Bairro Centro, Rua José Peters, 175',
      'taxa_entrega' => 10.00,
    ];

    // Act
    $resultados = $pedidoService->update($args);

    //Assert
    $this->assertEquals(StatusPedido::CANCELADO, $pedidoMock->status);
    $this->assertEquals(['message' => 'Pedido atualizado com sucesso'], $resultados);
  }
}
