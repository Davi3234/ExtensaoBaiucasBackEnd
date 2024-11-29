<?php

namespace Tests\Davi\User;

use App\Enums\StatusPedido;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Repositories\ICategoriaRepository;
use App\Repositories\IPedidoItemRepository;
use App\Repositories\IProdutoRepository;
use App\Services\ProdutoService;
use Core\Exception\Exception;
use Exception\ValidationException;
use Provider\Zod\ZodParseException;

class ProductTest extends TestCase {

  #[Test]
  public function deveDispararExcecaoParaNomeInvalido() {

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = '';
    $descricao = 'Hambúrguer, queijo, cebola, bacon, alface, tomate e pão';
    $valor = 22;
    $categoria = new Categoria(1, 'Comida');
    $ativo = true;

    $produto = new Produto(
      nome: $nome,
      descricao: $descricao,
      valor: $valor,
      categoria: $categoria,
      ativo: $ativo,
      dataInclusao: date('Y-m-d')
    );

    //Act

    //Configuração do Mock
    $produtoRepository = $this->createMock(IProdutoRepository::class);
    $categoriaRepository = $this->createMock(ICategoriaRepository::class);
    $pedidoItemRepository = $this->createMock(IPedidoItemRepository::class);

    $categoriaRepository->method('findById')
      ->with($categoria->getId())
      ->willReturn($categoria);

    $produtoRepository->method('create')
      ->with($produto)
      ->willReturn($produto);

    $produtoRepository->method('findByDescription')
      ->willReturn(null);

    $produtoService = new ProdutoService($produtoRepository, $categoriaRepository, $pedidoItemRepository);

    $response = $produtoService->createProduto([
      'nome' => $nome,
      'descricao' => $descricao,
      'valor' => $valor,
      'id_categoria' => $categoria->getId(),
      'ativo' => $ativo
    ]);
  }

  #[Test]
  public function deveDispararExcecaoParaDescricaoInvalida() {

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = 'X-Bacon';
    $descricao = '';
    $valor = 22;
    $categoria = new Categoria(1, 'Comida');
    $ativo = true;

    $produto = new Produto(
      nome: $nome,
      descricao: $descricao,
      valor: $valor,
      categoria: $categoria,
      ativo: $ativo,
      dataInclusao: date('Y-m-d')
    );

    //Act

    //Configuração do Mock
    $produtoRepository = $this->createMock(IProdutoRepository::class);
    $categoriaRepository = $this->createMock(ICategoriaRepository::class);
    $pedidoItemRepository = $this->createMock(IPedidoItemRepository::class);

    $categoriaRepository->method('findById')
      ->with($categoria->getId())
      ->willReturn($categoria);

    $produtoRepository->method('create')
      ->with($produto)
      ->willReturn($produto);

    $produtoRepository->method('findByDescription')
      ->willReturn(null);

    $produtoService = new ProdutoService($produtoRepository, $categoriaRepository, $pedidoItemRepository);

    $response = $produtoService->createProduto([
      'nome' => $nome,
      'descricao' => $descricao,
      'valor' => $valor,
      'id_categoria' => $categoria->getId(),
      'ativo' => $ativo
    ]);
  }

  #[Test]
  public function deveDispararExcecaoParaValorInvalido() {

    $this->expectException(ZodParseException::class);

    //Arrange
    $nome = 'X-Bacon';
    $descricao = 'Hambúrguer, queijo, cebola, bacon, alface, tomate e pão';
    $valor = -2;
    $categoria = new Categoria(1, 'Comida');
    $ativo = true;

    $produto = new Produto(
      nome: $nome,
      descricao: $descricao,
      valor: $valor,
      categoria: $categoria,
      ativo: $ativo,
      dataInclusao: date('Y-m-d')
    );

    //Act

    //Configuração do Mock
    $produtoRepository = $this->createMock(IProdutoRepository::class);
    $categoriaRepository = $this->createMock(ICategoriaRepository::class);
    $pedidoItemRepository = $this->createMock(IPedidoItemRepository::class);

    $categoriaRepository->method('findById')
      ->with($categoria->getId())
      ->willReturn($categoria);

    $produtoRepository->method('create')
      ->with($produto)
      ->willReturn($produto);

    $produtoRepository->method('findByDescription')
      ->willReturn(null);

    $produtoService = new ProdutoService($produtoRepository, $categoriaRepository, $pedidoItemRepository);

    $response = $produtoService->createProduto([
      'nome' => $nome,
      'descricao' => $descricao,
      'valor' => $valor,
      'id_categoria' => $categoria->getId(),
      'ativo' => $ativo
    ]);
  }

  #[Test]
  public function deveCriarProduto() {

    //Arrange
    $nome = 'X-Bacon';
    $descricao = 'Hambúrguer, queijo, cebola, bacon, alface, tomate e pão';
    $valor = 22;
    $categoria = new Categoria(1, 'Comida');
    $ativo = true;

    $produto = new Produto(
      nome: $nome,
      descricao: $descricao,
      valor: $valor,
      categoria: $categoria,
      ativo: $ativo,
      dataInclusao: date('Y-m-d')
    );

    //Act

    //Configuração do Mock
    $produtoRepository = $this->createMock(IProdutoRepository::class);
    $categoriaRepository = $this->createMock(ICategoriaRepository::class);
    $pedidoItemRepository = $this->createMock(IPedidoItemRepository::class);

    $categoriaRepository->method('findById')
      ->with($categoria->getId())
      ->willReturn($categoria);

    $produtoRepository->method('create')
      ->with($produto)
      ->willReturn($produto);

    $produtoRepository->method('findByDescription')
      ->willReturn(null);

    $produtoService = new ProdutoService($produtoRepository, $categoriaRepository, $pedidoItemRepository);

    $response = $produtoService->createProduto([
      'nome' => $nome,
      'descricao' => $descricao,
      'valor' => $valor,
      'id_categoria' => $categoria->getId(),
      'ativo' => $ativo
    ]);

    $this->assertTrue(['message' => 'Produto cadastrado com sucesso'] == $response);
  }

  #[Test]
  public function deveDispararExcecaoParaProdutoComPedidoEmAberto() {

    //Arrange
    $nome = 'X-Bacon';
    $descricao = 'Hambúrguer, queijo, cebola, bacon, alface, tomate e pão';
    $valor = 22;
    $categoria = new Categoria(1, 'Comida');
    $ativo = true;

    $produto = new Produto(
      id: 1,
      nome: $nome,
      descricao: $descricao,
      valor: $valor,
      categoria: $categoria,
      ativo: $ativo,
      dataInclusao: date('Y-m-d')
    );

    //Act

    //Configuração do Mock
    $produtoRepository = $this->createMock(IProdutoRepository::class);
    $categoriaRepository = $this->createMock(ICategoriaRepository::class);
    $pedidoItemRepository = $this->createMock(IPedidoItemRepository::class);

    $categoriaRepository->method('findById')
      ->with($categoria->getId())
      ->willReturn($categoria);

    $pedidoItemRepository->method('findByIdProdutoAberto')
      ->with($produto->getIdProduto())
      ->willReturn([
        new PedidoItem(
          id: 1,
          produto: $produto,
          pedido: new Pedido(
            id: 1,
            dataPedido: '25-11-2024',
            cliente: new User(),
            status: StatusPedido::EM_PREPARO
          )
        )
      ]);

    $pedidoItemRepository->method('findByIdProdutoAndamento')
      ->with($produto->getIdProduto())
      ->willReturn([]);

    $produtoRepository->method('create')
      ->with($produto)
      ->willReturn($produto);

    $produtoRepository->method('findById')
      ->with($produto->getIdProduto())
      ->willReturn($produto);

    $produtoRepository->method('findByDescription')
      ->willReturn(null);

    $produtoService = new ProdutoService($produtoRepository, $categoriaRepository, $pedidoItemRepository);

    $this->expectException(ValidationException::class);

    try {
      $produtoService->updateProduto([
        'id' => 1,
        'nome' => $nome,
        'descricao' => $descricao,
        'valor' => $valor,
        'id_categoria' => $categoria->getId(),
        'ativo' => $ativo
      ]);
    } catch (Exception $err) {
      $this->assertNotEmpty($err->getCausesFromOrigin('status', 'andamento'));

      throw $err;
    }
  }
}
