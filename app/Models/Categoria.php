<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity]
#[Table(name: 'categorias')]
class Categoria extends Model
{

    #[OneToMany(targetEntity: Produto::class)]
    #[JoinColumn(name: 'id_categoria', referencedColumnName: 'id')]

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id_categoria;

	#[Column]
	public string $descricao_categoria;

	public function __construct($id_categoria = 0, $descricao_categoria = null){
		$this->id_categoria = $id_categoria;
		$this->descricao_categoria = $descricao_categoria;
	}

	public function getIdCategoria(): int {
		return $this->id_categoria;
	}

	public function setIdCategoria(int $value){
		$this->id_categoria = $value;
	}

	public function getDescricaoCategoria(): string{
		return $this->id_categoria;
	}

	public function setDescricaoCategoria(string $value){
		$this->descricao_categoria = $value;
	}
}
