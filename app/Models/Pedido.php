<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;
use App\Enums\FormaPagamento;
use App\Enums\StatusPedido;
use App\Enums\TipoEntrega;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[Table(name: 'pedidos')]
class Pedido extends Model {

	#[Id]
	#[GeneratedValue]
	#[Column]
	private int $id;

	#[Column(name: 'data_pedido')]
	private string $dataPedido;

	#[ManyToOne(targetEntity: User::class)]
	#[JoinColumn(name: 'id_cliente', referencedColumnName: 'id')]
	private User $cliente;

	#[Column(name: 'valor_total')]
	private float $valorTotal;

	#[Column(type: 'string', enumType: StatusPedido::class)]
	private string $status;

	#[Column(name: 'forma_pagamento', type: 'string', enumType: FormaPagamento::class)]
	private string $formaPagamento;

	#[Column]
	private string $observacoes;

	#[Column(type: 'string', enumType: TipoEntrega::class)]
	private string $tipo;

	#[Column(name: 'endereco_entrega')]
	private string $enderecoEntrega;

	#[Column(name: 'taxa_entrega')]
	private float $taxaEntrega;

	public function __construct($id = 0, $dataPedido = null, $cliente = 0, $valorTotal  = 0, $status = '', $formaPagamento = '', $observacoes = '', $tipo = '', $enderecoEntrega = '', $taxaEntrega = 0,) {
		$this->id = $id;
		$this->dataPedido = $dataPedido;
		$this->cliente = $cliente;
		$this->valorTotal  = $valorTotal;
		$this->status = $status;
		$this->formaPagamento = $formaPagamento;
		$this->observacoes = $observacoes;
		$this->tipo = $tipo;
		$this->enderecoEntrega = $enderecoEntrega;
		$this->taxaEntrega = $taxaEntrega;
	}

	//Id do Pedido
	public function getIdPedido(): int {
		return $this->id;
	}

	public function setIdPedido(int $value) {
		$this->id = $value;
	}

	public function getDataPedido(): string {
		return $this->dataPedido;
	}

	public function setDataPedido(string $value) {
		$this->dataPedido = $value;
	}

	public function getCliente(): User {
		return $this->cliente;
	}

	public function setCliente(User $value) {
		$this->cliente = $value;
	}

	public function getValorTotal(): float {
		return $this->valorTotal;
	}

	public function setValorTotal(float $value) {
		$this->valorTotal = $value;
	}

	//Status = Enum
	public function getStatus(): string {
		return $this->status;
	}

	public function setStatus(string $value) {
		$this->status = $value;
	}

	//Forma de Pagamento = Enum
	public function getFormaPagamento(): string {
		return $this->formaPagamento;
	}

	public function setFormaPagamento(string $value) {
		$this->formaPagamento = $value;
	}

	//Observações
	public function getObservacoes(): string {
		return $this->observacoes;
	}

	public function setObservacoes(string $value) {
		$this->observacoes = $value;
	}

	//Tipo = Enum
	public function getTipo(): string {
		return $this->tipo;
	}

	public function setTipo(string $value) {
		$this->tipo = $value;
	}

	//Endereço de entrega
	public function getEnderecoEntrega(): string {
		return $this->enderecoEntrega;
	}

	public function setEnderecoEntrega(string $value) {
		$this->enderecoEntrega = $value;
	}

	//Taxa de Entrega
	public function getTaxaEntrega(): float {
		return $this->taxaEntrega;
	}

	public function setTaxaEntrega(float $value) {
		$this->taxaEntrega = $value;
	}
}
