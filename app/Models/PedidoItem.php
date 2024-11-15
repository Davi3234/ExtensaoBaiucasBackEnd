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
#[Table(name: 'itens_pedidos')]
class PedidoItem extends Model {

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id_pedido_item;

	#[ManyToOne(targetEntity: Pedido::class)]
	#[JoinColumn(name: 'pedido', referencedColumnName: 'id_pedido')]
	public Pedido $pedido;

	#[ManyToOne(targetEntity: Produto::class)]
	#[JoinColumn(name: 'produto', referencedColumnName: 'id_produto')]
	public Produto $produto;

	#[Column]
	public float $valor_item;

	#[Column]
	public string $observacoes_item;

	public function __construct(array $args = []) {
		$this->id_pedido_item  = 0;
		$this->produto = 0;
		$this->pedido  = null;
		$this->valor_item = 0;
		$this->observacoes_item = '';
	}

	public function getIdPedidoItem(): int {
		return $this->id_pedido_item;
	}

	public function setIdPedidoItem(int $value) {
		$this->id_pedido_item = $value;
	}

	public function getPedido(): Pedido {
		return $this->pedido;
	}

	public function setPedido(Pedido $value) {
		$this->pedido = $value;
	}

	public function getProduto(): Produto {
		return $this->produto;
	}

	public function setProduto(Produto $value) {
		$this->produto = $value;
	}

	public function getValorItem(): float {
		return $this->valor_item;
	}

	public function setValorItem(float $value) {
		$this->valor_item = $value;
	}

	public function getObservacoesItem(): string {
		return $this->observacoes_item;
	}

	public function setObservacoesItem(string $value) {
		$this->observacoes_item = $value;
	}
}
