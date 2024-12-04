<?php

namespace Tests\Daiane\Integracao;

use PHPUnit\Framework\TestCase;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use PHPUnit\Framework\Attributes\Test;

class CategoryIntegracaoTest extends TestCase
{
    private CategoriaRepository $categoriaRepository;

    protected function setUp(): void
    {
        $this->categoriaRepository  = new CategoriaRepository();
    }

    #[Test]
    public function manterCategorias()
    {
        //Caso de teste 01: Verificar se o sistema insere corretamente as categorias no banco de dados
        //Arrange
        $descricao1 = 'Alimentos';
        $descricao2 = 'Bebidas';

        //Act
        $this->categoriaRepository->create(new Categoria(
            descricao: $descricao1
        ));

        $this->categoriaRepository->create(new Categoria(
            descricao: $descricao2
        ));

        $categorias = $this->categoriaRepository->findMany();

        //Assert
        $this->assertCount(2, $categorias);
        $this->assertEquals($descricao1, $categorias[0]->getDescricao());
        $this->assertEquals($descricao2, $categorias[1]->getDescricao());

        //Caso de teste 02: Verificar se o sistema altera corretamente as categorias com as novas descrições informadas
        //Arrange
        $id = 1;
        $categoriaUpdate = $this->categoriaRepository->findById($id);

        //Act
        $categoriaAlterada = $this->categoriaRepository->update($categoriaUpdate);

        //Assert
        $this->assertEquals($categoriaAlterada->getId(), $id);

        //Caso de teste 03: Verificar se o sistema deleta corretamente as categorias que foram solicitadas para deletar
        //Arrange
        $id = 1;

        //Act
        $this->categoriaRepository->deleteById($id);

        $categoriaDeletada = $this->categoriaRepository->findById($id);

        //Assert
        $this->assertEquals($categoriaDeletada, null);
    }
}
