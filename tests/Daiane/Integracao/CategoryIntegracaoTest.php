<?php

namespace Tests\Categoria;

use PHPUnit\Framework\TestCase;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use PHPUnit\Framework\Attributes\Test;

class CategoryIntegracaoTest extends TestCase
{

    #[Test]
    public function manterCategorias()
    {
        //Caso de teste 01: Verificar se o sistema insere corretamente as categorias no banco de dados
        //Arrange
        $categoriaRepository  = new CategoriaRepository();

        //Act
        $this->$categoriaRepository->create(new Categoria(
            descricao: 'Alimentos'
        ));

        $this->$categoriaRepository->create(new Categoria(
            descricao: 'Bebidas'
        ));

        $categorias = $this->$categoriaRepository->findMany();

        //Assert
        $this->assertCount(2, $categorias);
        $this->assertEquals('Alimentos', $categorias[0]->getDescricao());
        $this->assertEquals('Bebidas', $categorias[1]->getDescricao());

        //Caso de teste 02: Verificar se o sistema altera corretamente as categorias com as novas descrições informadas
        //Arrange
        $id = 1;
        $categoriaUpdate = $this->$categoriaRepository->findById($id);

        //Act
        $categoriaAlterada = $this->$categoriaRepository->update($categoriaUpdate);

        //Assert
        $this->assertEquals($categoriaAlterada->getId(), $id);

        //Caso de teste 03: Verificar se o sistema deleta corretamente as categorias que foram solicitadas para deletar
        //Arrange
        $id = 1;

        //Act
        $this->$categoriaRepository->deleteById($id);

        $userDeleted = $this->$categoriaRepository->findById($id);

        //Assert
        $this->assertEquals($userDeleted, null);
    }
}
