<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;

#[Entity]
#[Table(name: 'categorias')]
class Categoria extends Model {

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id;

	#[Column]
	public string $descricao;

	public function __construct($id = 0, $descricao = null) {
		$this->id = $id;
		$this->descricao = $descricao;
	}

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $value) {
		$this->id = $value;
	}

	public function getDescricao(): string {
		return $this->descricao;
	}

	public function setDescricao(string $value) {
		$this->descricao = $value;
	}
}
