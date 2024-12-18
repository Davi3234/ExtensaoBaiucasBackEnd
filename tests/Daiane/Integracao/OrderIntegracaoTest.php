<?php

namespace Tests\Daiane\Integracao;

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

class OrderIntegracaoTest extends TestCase
{

  #[Test]
  public function CreatePedido()
  {

    //Caso de teste 01 (Integração): Garantir que o pedido seja inserido corretamente no banco de dados com todas as informações do pedido informadas

    // Arrange
    $userRepository = new UserRepository();
    $produtoRepository = new ProdutoRepository();
    $pedidoRepository = new PedidoRepository();
    $pedidoItemRepository = new PedidoItemRepository();

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

    $pedidoItem = new PedidoItem(
      produto: $produto,
      pedido: $pedido,
      valorItem: 100.00,
      observacoesItem: 'Com bastante bacon'
    );

    //Act
    $produtoRepository->create($produto);
    $pedidoRepository->create($pedido);
    $pedidoItemRepository->create($pedidoItem);
    $pedidoAct = $pedidoRepository->findById($pedido->getIdPedido());

    // Assert
    $this->assertEquals($pedido, $pedidoAct, 'Os pedido inserido não confere com os dados informados para inserção');
    $this->assertNotNull($pedidoAct, 'Pedido não encontrado no repositório.');
    $this->assertEquals($pedido->getIdPedido(), $pedidoAct->getIdPedido(), 'O ID do pedido não é o mesmo.');
    $this->assertEquals($pedido->getCliente()->getId(), $pedidoAct->getCliente()->getId(), 'O cliente do pedido não é o mesmo.');
  }
}
