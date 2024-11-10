<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;

#[Entity]
#[Table(name: 'itens_pedidos')]
class PedidoItem extends Model {

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id_pedido;

	#[Column]
	public int $id_item;

	#[Column]
	public float $valor_item;

	#[Column]
	public string $observacoes_item;

	public function __construct(array $args = []) {
		$this->id_pedido  = 0;
		$this->id_item = 0;
		$this->valor_item = 0;
		$this->observacoes_item = '';

		$this->povoaPropriedades($args);
	}

	#[\Override]
	function __load(array $raw) {
		$this->id_pedido = $raw['id_pedido'];
		$this->id_item  = $raw['id_item'];
		$this->valor_item = $raw['valor_item'];
		$this->observacoes_item = $raw['observacoes_item'];
	}

	protected function povoaPropriedades(array $args = []) {
		foreach ($args as $prop => $value) {
			$this->$prop = $value;
		}
	}

	//Id do Pedido
	public function getIdPedido(): int {
		return $this->id_pedido;
	}

	public function setIdPedido(int $value) {
		$this->id_pedido = $value;
	}

	//Id do Item
	public function getIdItem(): int {
		return $this->id_item;
	}

	public function setIdItem(int $value) {
		$this->id_item = $value;
	}

	//Valor do Item
	public function getValorItem(): float {
		return $this->valor_item;
	}

	public function setValorItem(float $value) {
		$this->valor_item = $value;
	}

	//Observacoes do Item, por exemplo = sem cebola, bem passado
	public function getObservacoesItem(): string {
		return $this->observacoes_item;
	}

	public function setObservacoesItem(string $value) {
		$this->observacoes_item = $value;
	}
}
