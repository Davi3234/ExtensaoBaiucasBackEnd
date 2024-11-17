<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[Table(name: 'produtos')]
class Produto extends Model {

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id;

	#[Column]
	public string $nome;

	#[Column]
	public string $descricao;

	#[Column]
	public float $valor;

	//#[ManyToOne(targetEntity: Categoria::class)]
	#[ManyToOne(targetEntity: Categoria::class, cascade: ['persist'])]
	#[JoinColumn(name: 'id_categoria', referencedColumnName: 'id')]
	public ?Categoria $categoria;


	#[Column(options: ['default' => true])]
	public bool $ativo;

	#[Column(name: 'data_inclusao')]
	public string $dataInclusao;

	public function __construct($id = 0, $nome = '', $descricao = '', $valor  = 0, $categoria = null, $ativo = true, $dataInclusao = '') {
		$this->id = $id;
		$this->nome = $nome;
		$this->descricao = $descricao;
		$this->valor = $valor;
		$this->categoria = $categoria;
		$this->ativo = $ativo;
		$this->dataInclusao = $dataInclusao;
	}

	public function getIdProduto(): int {
		return $this->id;
	}

	public function setIdProduto(int $value) {
		$this->id = $value;
	}

	public function getNome(): string {
		return $this->nome;
	}

	public function setNome(string $value) {
		$this->nome = $value;
	}

	public function getDescricao(): string {
		return $this->descricao;
	}

	public function setDescricao(string $value) {
		$this->descricao = $value;
	}

	public function getValor(): float {
		return $this->valor;
	}

	public function setValor(float $value) {
		$this->valor = $value;
	}

	public function getCategoria(): Categoria {
		return $this->categoria;
	}

	public function setCategoria(Categoria $value) {
		$this->categoria = $value;
	}

	public function getDataInclusao(): string {
		return $this->dataInclusao;
	}

	public function setDataInclusao(string $value) {
		$this->dataInclusao = $value;
	}

	public function getAtivo(): bool {
		return $this->ativo;
	}

	public function setAtivo(bool $value) {
		$this->ativo = $value;
	}
}
