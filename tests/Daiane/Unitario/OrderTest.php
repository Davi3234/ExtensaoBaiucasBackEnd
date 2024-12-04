<?php

namespace Tests\Daiane\Unitario;

use App\Enums\FormaPagamento;
use App\Enums\StatusPedido;
use App\Enums\TipoEntrega;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\PedidoItem;
use App\Services\PedidoService;
use App\Repositories\IPedidoRepository;
use App\Repositories\IUserRepository;
use App\Repositories\IProdutoRepository;
use App\Services\PedidoItemService;
use Exception\ValidationException;

class OrderTest extends TestCase
{

  ///////////////////////////////////////////Testes Unitários/////////////////////////////////////////////////

  #[Test]
  public function GetPedidosPorStatus()
  {

    //Caso de teste 01 (Unitário): Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status “Em preparo”


    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedidoRepositoryMock
      ->method('findManyByStatus')
      ->with(StatusPedido::EM_PREPARO->value)
      ->willReturn([
        new Pedido(
          id: 1,
          status: StatusPedido::EM_PREPARO,
          observacoes: '',
          tipo: TipoEntrega::DELIVERY,
          cliente: $user,
          dataPedido: '2024-10-10',
          valorTotal: 1040
        ),
        new Pedido(
          id: 2,
          status: StatusPedido::EM_PREPARO,
          observacoes: 'Sem troco',
          tipo: TipoEntrega::NO_LOCAL,
          cliente: $user,
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

    //Caso de teste 02 (Unitário): Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status “Cancelado”

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedidoRepositoryMock
      ->method('findManyByStatus')
      ->with(StatusPedido::CANCELADO->value)
      ->willReturn([
        new Pedido(
          id: 1,
          status: StatusPedido::CANCELADO,
          observacoes: 'Cancelado por demora na entrega',
          tipo: TipoEntrega::DELIVERY,
          cliente: $user,
          dataPedido: '2024-10-26',
          valorTotal: 2345
        ),
        new Pedido(
          id: 2,
          status: StatusPedido::CANCELADO,
          observacoes: '',
          tipo: TipoEntrega::NO_LOCAL,
          cliente: $user,
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

    //Caso de teste 03 (Unitário): Validar se o método GetPedidosPorStatus() retorna corretamente os dados do pedido com status Finalizado

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedidoRepositoryMock
      ->method('findManyByStatus')
      ->with(StatusPedido::FINALIZADO->value)
      ->willReturn([
        new Pedido(
          id: 1,
          status: StatusPedido::FINALIZADO,
          observacoes: 'Entrega realizada com sucesso!',
          tipo: TipoEntrega::DELIVERY,
          cliente: $user,
          dataPedido: '2024-08-10',
          valorTotal: 35
        ),
        new Pedido(
          id: 2,
          status: StatusPedido::FINALIZADO,
          observacoes: 'Com gorjeta',
          tipo: TipoEntrega::NO_LOCAL,
          cliente: $user,
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
  }

  #[Test]
  public function ExibirItensPedido()
  {

    //Caso de teste 01 (Unitário): Verificar se o sistema exibe corretamente os itens presentes em um pedido que contém apenas um item

    // Arrange
    $pedidoId = 1;

    $produto = new Produto(
      id: 1,
      nome: 'X-Bacon',
      descricao: 'X-Bacon Duplo',
      valor: 30,
      categoria: new Categoria(id: 1, descricao: "Alimentos"),
      ativo: true,
      dataInclusao: '2024-11-27'
    );

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido = new Pedido(
      id: $pedidoId,
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 100.00,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: 'Sem troco',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua 123, Bairro Centro, Apiúna'
    );

    $pedidoItem = new PedidoItem(
      id: 1,
      produto: $produto,
      pedido: $pedido,
      valorItem: 100.00,
      observacoesItem: 'Com bastante bacon'
    );

    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoRepositoryMock->method('findById')
      ->with($pedidoId)
      ->willReturn($pedido);

    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $pedidoItemServiceMock->method('findManyByIdPed')
      ->with($pedidoId)
      ->willReturn([$pedidoItem]);

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
          'name' => 'Daiane',
        ],
        'items' => [
          [
            'id' => 1,
            'product' => [
              'id' => 1,
              'name' => 'X-Bacon',
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

    //Caso de teste 02 (Unitário): Verificar se o sistema exibe corretamente os itens presentes em um pedido que contém três itens

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido = new Pedido(
      id: 1,
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 90.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: 'No capricho',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $produto1 = new Produto(
      id: 1,
      nome: 'Batata com Bacon',
      valor: 45,
      categoria: new Categoria(id: 1, descricao: "Alimentos"),
      ativo: true,
      dataInclusao: '2024-11-27'
    );

    $produto2 = new Produto(
      id: 2,
      nome: 'Refrigerante Coca-Cola',
      valor: 45,
      categoria: new Categoria(id: 2, descricao: "Bebidas"),
      ativo: true,
      dataInclusao: '2024-11-27'
    );

    $produto3 = new Produto(
      id: 3,
      nome: 'X-burguer',
      valor: 45,
      categoria: new Categoria(id: 1, descricao: "Alimentos"),
      ativo: true,
      dataInclusao: '2024-11-27'
    );

    $pedidoItem1 = new PedidoItem(
      id: 1,
      produto: $produto1,
      pedido: $pedido,
      valorItem: 45.0,
      observacoesItem: 'Com Pouco Bacon'
    );

    $pedidoItem2 = new PedidoItem(
      id: 2,
      produto: $produto2,
      pedido: $pedido,
      valorItem: 15.0,
      observacoesItem: 'Copo com limão e gelo'
    );

    $pedidoItem3 = new PedidoItem(
      id: 3,
      produto: $produto3,
      pedido: $pedido,
      valorItem: 30.0,
      observacoesItem: 'Com dois hamburguers'
    );

    $pedidoItemServiceMock
      ->method('findManyByIdPed')
      ->with(1)
      ->willReturn([$pedidoItem1, $pedidoItem2, $pedidoItem3]);

    $pedidoRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($pedido);

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
          'name' => 'Daiane',
        ],
        'items' => [
          [
            'id' => 1,
            'product' => [
              'id' => 1,
              'name' => 'Batata com Bacon',
            ],
            'price' => 45.0,
            'observation' => 'Com Pouco Bacon',
          ],
          [
            'id' => 2,
            'product' => [
              'id' => 2,
              'name' => 'Refrigerante Coca-Cola',
            ],
            'price' => 15.0,
            'observation' => 'Copo com limão e gelo',
          ],
          [
            'id' => 3,
            'product' => [
              'id' => 3,
              'name' => 'X-burguer',
            ],
            'price' => 30.0,
            'observation' => 'Com dois hamburguers',
          ],
        ],
        'totalPrice' => 90.0,
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

  #[Test]
  public function FiltrarPedidosPorData()
  {

    //Caso de teste 01 (Unitário): Verificar se o filtro de pedidos por data está funcionando corretamente trazendo somente os pedidos da data informada

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido1 = new Pedido(
      id: 1,
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 90.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: '',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $pedido2 = new Pedido(
      id: 2,
      dataPedido: '2024-11-28',
      cliente: $user,
      valorTotal: 50.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: '',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $pedido3 = new Pedido(
      id: 3,
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 150.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: '',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $pedidoRepositoryMock
      ->method('findByDateRange')
      ->with('2024-11-27', '2024-11-27')
      ->willReturn([$pedido1, $pedido3]);

    $pedidoItemServiceMock->method('findManyByIdPed')->willReturn([]);

    $pedidoService = new PedidoService(
      $pedidoRepositoryMock,
      $pedidoItemServiceMock,
      $this->createMock(IUserRepository::class),
      $this->createMock(IProdutoRepository::class)
    );

    // Act
    $resultados = $pedidoService->filtrarPedidosPorData('2024-11-27', '2024-11-27');

    $resultadoEsperado = [
      [
        'id' => 1,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Daiane',
        ],
        'valorTotalPedido' => 90.0,
      ],
      [
        'id' => 3,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Daiane',
        ],
        'valorTotalPedido' => 150.0,
      ]
    ];

    // Assert
    $this->assertEquals($resultadoEsperado, $resultados);

    //Caso de teste 02 (Unitário): Verificar se o filtro de pedidos por data está funcionando corretamente trazendo somente os pedidos informados no intervalo de datas

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido1 = new Pedido(
      id: 1,
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 90.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: '',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $pedido2 = new Pedido(
      id: 2,
      dataPedido: '2024-11-28',
      cliente: $user,
      valorTotal: 50.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: '',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $pedido3 = new Pedido(
      id: 3,
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 150.0,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: '',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua José Peters, 435, Centro, Apiúna'
    );

    $pedidoRepositoryMock
      ->method('findByDateRange')
      ->with('2024-11-27', '2024-11-28')
      ->willReturn([$pedido1, $pedido2, $pedido3]);

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
          'nome' => 'Daiane',
        ],
        'valorTotalPedido' => 90.0,
      ],
      [
        'id' => 2,
        'data_pedido' => '2024-11-28',
        'cliente' => [
          'id' => 1,
          'nome' => 'Daiane',
        ],
        'valorTotalPedido' => 50.0,
      ],
      [
        'id' => 3,
        'data_pedido' => '2024-11-27',
        'cliente' => [
          'id' => 1,
          'nome' => 'Daiane',
        ],
        'valorTotalPedido' => 150.0,
      ]
    ];

    // Assert
    $this->assertEquals($resultadoEsperado, $resultados);

    //Caso de teste 03 (Unitário): Verificar se o filtro de pedidos por data está funcionando corretamente mostrando um erro ao informar uma data que não possuem pedidos registrados

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);

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

    // Act
    $resultado = $pedidoService->filtrarPedidosPorData('2024-11-29', '2024-11-30');

    // Assert
    $this->assertEmpty($resultado, 'Esperava-se que o resultado fosse vazio para datas sem pedidos.');
  }

  #[Test]
  public function UpdateStatusPedido()
  {

    //Caso de teste 01 (Unitário): Verificar se a atualização do status ocorre corretamente quando o pedido está com status "Em Preparo" e é alterado para "Finalizado"

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido = new Pedido(
      id: 1,
      dataPedido: '2024-11-15',
      cliente: $user,
      valorTotal: 100.00,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::PIX,
      observacoes: 'Pedido em andamento',
      tipo: TipoEntrega::NO_LOCAL,
      enderecoEntrega: 'Rua Exemplo, 123',
      taxaEntrega: 10.00
    );

    $pedidoRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($pedido);

    $userRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($user);

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
    $this->assertEquals(StatusPedido::FINALIZADO, $pedido->status);
    $this->assertEquals(['message' => 'Pedido atualizado com sucesso'], $resultados);

    //Caso de teste 02 (Unitário): Verificar se a tentativa de atualizar o status de um pedido com status "Cancelado" para novo status "Em Preparo" resulta em erro

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido = new Pedido(
      id: 1,
      dataPedido: '2024-11-15',
      cliente: $user,
      valorTotal: 100.00,
      status: StatusPedido::CANCELADO,
      formaPagamento: FormaPagamento::PIX,
      observacoes: 'Pedido cancelado pelo cliente',
      tipo: TipoEntrega::NO_LOCAL,
      enderecoEntrega: 'Rua Exemplo, 123',
      taxaEntrega: 10.00
    );

    $userRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($user);

    $pedidoRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($pedido);

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

    //Caso de teste 03 (Unitário): Verificar se a atualização de status de um pedido com status "Em Preparo" para "Cancelado" é realizada corretamente

    // Arrange
    $pedidoRepositoryMock = $this->createMock(IPedidoRepository::class);
    $pedidoItemServiceMock = $this->createMock(PedidoItemService::class);
    $userRepositoryMock = $this->createMock(IUserRepository::class);

    $password = 'Dai2349453';
    $user = new User(
      id: 1,
      name: 'Daiane',
      login: 'daiane@gmail.com',
      cpf: '111.287.078-91',
      endereco: 'Rua 25 de janeiro',
      password: md5($password),
      active: true
    );

    $pedido = new Pedido(
      id: 1,
      dataPedido: '2024-11-15',
      cliente: $user,
      valorTotal: 100.00,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::PIX,
      observacoes: 'Pedido em andamento',
      tipo: TipoEntrega::NO_LOCAL,
      enderecoEntrega: 'Rua Exemplo, 123',
      taxaEntrega: 10.00
    );

    $userRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($user);

    $pedidoRepositoryMock
      ->method('findById')
      ->with(1)
      ->willReturn($pedido);

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
    $this->assertEquals(StatusPedido::CANCELADO, $pedido->status);
    $this->assertEquals(['message' => 'Pedido atualizado com sucesso'], $resultados);
  }
}
