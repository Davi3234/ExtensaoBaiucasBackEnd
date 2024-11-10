<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;

#[Entity]
#[Table(name: 'produtos')]
class Produto extends Model {

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
	public int $id_categoria;

	#[Column(options: ['default' => true])]
	public bool $ativo;

    #[Column]
	public string $data_inclusao;

	public function __construct(array $args = []) {
		$this->id_produto = 0;
		$this->nome = '';
		$this->descricao = '';
		$this->valor = 0;
		$this->id_categoria = 0;
        $this->ativo = true;
        $this->data_inclusao = 0;

		$this->povoaPropriedades($args);
	}

	#[\Override]
	function __load(array $raw) {
		$this->id_produto = $raw['id_produto'];
		$this->nome = $raw['nome'];
		$this->descricao = $raw['descricao'];
		$this->valor = $raw['valor'];
		$this->id_categoria = $raw['id_categoria'];
        $this->ativo = $raw['ativo'];
        $this->data_inclusao = $raw['data_inclusao'];
	}

	protected function povoaPropriedades(array $args = []) {
		foreach ($args as $prop => $value) {
			$this->$prop = $value;
		}
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

	public function getIdCategoria(): int {
		return $this->id_categoria;
	}

    public function setIdCategoria(int $value) {
		$this->id_categoria = $value;
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
