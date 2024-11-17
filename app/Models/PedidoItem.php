<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Common\Model;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[Table(name: 'itens_pedidos')]
class PedidoItem extends Model {

	#[Id]
	#[ManyToOne(targetEntity: Pedido::class, cascade: ['persist'])]
	#[JoinColumn(name: 'id_pedido', referencedColumnName: 'id', onDelete: "CASCADE")]
	public Pedido $pedido;

	#[Id]
	#[ManyToOne(targetEntity: Produto::class, cascade: ['persist'])]
	#[JoinColumn(name: 'id_produto', referencedColumnName: 'id')]
	public Produto $produto;

	#[Column(name: 'valor_item')]
	public float $valorItem;

	#[Column(name: 'observacoes_item')]
	public string $observacoesItem;

	public function __construct($produto = null, $pedido  = null, $valorItem = '', $observacoesItem = '') {
		$this->produto = $produto;
		$this->pedido  = $pedido;
		$this->valorItem = $valorItem;
		$this->observacoesItem = $observacoesItem;
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
		return $this->valorItem;
	}

	public function setValorItem(float $value) {
		$this->valorItem = $value;
	}

	public function getObservacoesItem(): string {
		return $this->observacoesItem;
	}

	public function setObservacoesItem(string $value) {
		$this->observacoesItem = $value;
	}
}
