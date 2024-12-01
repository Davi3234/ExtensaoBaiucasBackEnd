<?php

namespace Tests\Order;

use App\Enums\FormaPagamento;
use App\Enums\TipoUsuario;
use App\Enums\StatusPedido;
use App\Enums\TipoEntrega;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\PedidoItem;
use App\Models\Pedido;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\PedidoRepository;
use App\Repositories\PedidoItemRepository;
use App\Repositories\ProdutoRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PDO;

class OrderIntegracaoTest extends TestCase
{

  ///////////////////////////////////////////Testes de Integração/////////////////////////////////////////////////

  #[Test]
  public function GetPedidos()
  {
    $userRepository = new UserRepository();
    $produtoRepository = new ProdutoRepository();
    $pedidoRepository = new PedidoRepository();
    $pedidoItemRepository = new PedidoItemRepository();

    // Arrange
    $name = 'Daiane';
    $login = 'daiane@gmail.com';
    $cpf = '111.287.078-91';
    $endereco = 'Rua 25 de janeiro';
    $password = md5('Dai2349453');
    $active = true;
    $tipo = TipoUsuario::CLIENTE;

    $user = new User(
      name: $name,
      login: $login,
      cpf: $cpf,
      endereco: $endereco,
      password: $password,
      active: $active,
      tipo: $tipo
    );

    $userRepository->create($user);

    $produto = new Produto(
      nome: 'X-Bacon',
      descricao: 'X-Bacon Duplo',
      valor: 30.00,
      categoria: new Categoria(descricao: "Alimentos"),
      ativo: true,
      dataInclusao: '2024-11-27'
    );

    $produtoRepository->create($produto);

    $pedido = new Pedido(
      dataPedido: '2024-11-27',
      cliente: $user,
      valorTotal: 100.00,
      status: StatusPedido::EM_PREPARO,
      formaPagamento: FormaPagamento::CARTAO,
      observacoes: 'Sem troco',
      tipo: TipoEntrega::DELIVERY,
      enderecoEntrega: 'Rua 123, Bairro Centro, Apiúna'
    );

    $pedidoRepository->create($pedido);

    $pedidoItem = new PedidoItem(
      produto: $produto,
      pedido: $pedido,
      valorItem: 100.00,
      observacoesItem: 'Com bastante bacon'
    );

    $pedidoItemRepository->create($pedidoItem);

    // Act
    $pedidoAct = $pedidoRepository->findById($pedido->getIdPedido());

    // Assert
    $this->assertNotNull($pedidoAct, 'Pedido não encontrado no repositório.');
    $this->assertEquals($pedido->getIdPedido(), $pedidoAct->getIdPedido(), 'O ID do pedido não é o mesmo.');
    $this->assertEquals($pedido->getCliente()->getId(), $pedidoAct->getCliente()->getId(), 'O cliente do pedido não é o mesmo.');
  }

  //Caso de teste 02 (Integração): Verificar se o pedido com múltiplos itens é recuperado corretamente


  //Caso de teste 03 (Integração): Verificar se ao informar um pedido com 0 itens o método retorna erro


  #[Test]
  public function  CreatePedido()
  {

    //Caso de teste 01 (Integração): Garantir que o pedido seja inserido corretamente no banco de dados com todas as informações do pedido informadas

  }
}
