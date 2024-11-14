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

	
	#[ManyToOne(targetEntity: Categoria::class)]
	#[JoinColumn(name: 'id_categoria', referencedColumnName: 'id_categoria')]

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id_produto;

	#[Column]
	public string $nome;

    #[Column]
	public string $descricao;

	#[Column]
	public float $valor;

	#[Column]
	public Categoria $categoria;

	#[Column(options: ['default' => true])]
	public bool $ativo;

    #[Column]
	public string $data_inclusao;

	public function __construct($id_produto = 0, $nome = null, $descricao = 0, $valor  = 0, $categoria = 0, $ativo = true, $data_inclusao = '') {
		$this->id_produto = 0;
		$this->nome = '';
		$this->descricao = '';
		$this->valor = 0;
		$this->categoria = $categoria;
        $this->ativo = true;
        $this->data_inclusao = 0;

	}

	public function getIdProduto(): int {
		return $this->id_produto;
	}

	public function setIdProduto(int $value) {
		$this->id_produto = $value;
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
	    $this->descricao;
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

    public function setIdCategoria(Categoria $value) {
		$this->categoria = $value;
	}

    public function getDataInclusao(): string {
		return $this->data_inclusao;
	}

	public function setDataInclusao(string $value) {
		$this->data_inclusao = $value;
	}

	public function getAtivo(): bool {
		return $this->ativo;
	}

	public function setAtivo(bool $value) {
		$this->ativo = $value;
	}

}
