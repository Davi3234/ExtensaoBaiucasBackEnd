<?php

namespace Tests\Davi\User;

use App\Models\Categoria;
use App\Models\PedidoItem;
use App\Models\Produto;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Repositories\ICategoriaRepository;
use App\Repositories\IPedidoItemRepository;
use App\Repositories\IProdutoRepository;
use App\Services\UserService;
use App\Repositories\IUserRepository;
use App\Services\ProdutoService;
use Exception\ValidationException;
use Provider\Zod\ZodParseException;

class ProductTest extends TestCase
{

  #[Test]
  public function deveDispararExcecaoParaNomeInvalido()
  {

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
  public function deveDispararExcecaoParaDescricaoInvalida()
  {

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
  public function deveDispararExcecaoParaValorInvalido()
  {

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
  public function deveCriarProduto()
  {

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
  public function deveDispararExcecaoParaProdutoComPedidoEmAberto()
  {

    $this->expectException(ValidationException::class);

    //Arrange
    $id = 1;
    $nome = 'X-Bacon';
    $descricao = 'Hambúrguer, queijo, cebola, bacon, alface, tomate e pão';
    $valor = 22;
    $categoria = new Categoria(1, 'Comida');
    $ativo = true;

    $produto = new Produto(
      id: $id,
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
      ->willReturn([new PedidoItem(
        $produto,
        null,
        0
      )]);

    $pedidoItemRepository->method('findByIdProdutoAndamento')
      ->with($produto->getIdProduto())
      ->willReturn([]);

    $produtoRepository->method('update')
      ->with($produto)
      ->willReturn($produto);

    $produtoRepository->method('findByDescription')
      ->willReturn(null);

    $produtoService = new ProdutoService($produtoRepository, $categoriaRepository, $pedidoItemRepository);

    $response = $produtoService->updateProduto([
      'id' => $id,
      'nome' => $nome,
      'descricao' => $descricao,
      'valor' => $valor,
      'id_categoria' => $categoria->getId(),
      'ativo' => $ativo
    ]);
  }
}
