<?php

namespace App\Models;

use Doctrine\DBAL\Types\EnumType;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;
use App\Enum\FormaPagamento;
use App\Enum\StatusPedido;
use App\Enum\TipoEntrega;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[Table(name: 'pedidos')]
class Pedido extends Model
{

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id_pedido;

	#[Column]
	public string $data_pedido;

	#[ManyToOne(targetEntity: Cliente::class)]
    #[JoinColumn(name: 'id_cliente', referencedColumnName: 'id')]
	public Cliente $cliente;

	#[Column]
	public float $valor_total;

	#[Column(type: 'string', enumType: StatusPedido::class)]
	public string $status;

	#[Column(type: 'string', enumType: FormaPagamento::class)]
	public string $forma_pagamento;

	#[Column]
	public string $observacoes;

	#[Column(type: 'string', enumType: TipoEntrega::class)]
	public string $tipo;

	#[Column]
	public string $endereco_entrega;

	#[Column]
	public float $taxa_entrega;

	public function __construct($id_pedido = 0, $data_pedido = null, $cliente = 0, $valor_total  = 0, $status = '', $forma_pagamento = '', $observacoes = '', $tipo = '', $endereco_entrega = '', $taxa_entrega = 0,){
		$this->id_pedido = $id_pedido;
		$this->data_pedido = $data_pedido;
		$this->cliente = $cliente;
		$this->valor_total  = $valor_total;
		$this->status = $status;
		$this->forma_pagamento = $forma_pagamento;
		$this->observacoes = $observacoes;
		$this->tipo = $tipo;
		$this->endereco_entrega = $endereco_entrega;
		$this->taxa_entrega = $taxa_entrega;
	}

	//Id do Pedido
	public function getIdPedido(): int{
		return $this->id_pedido;
	}

	public function setIdPedido(int $value){
		$this->id_pedido = $value;
	}

	public function getDataPedido(): string{
		return $this->data_pedido;
	}

	public function setDataPedido(string $value){
		$this->data_pedido = $value;
	}

	public function getCliente(): Cliente{
		return $this->cliente;
	}

	public function setCliente(Cliente $value){
		$this->cliente = $value;
	}

	public function getValorTotal(): float{
		return $this->valor_total;
	}

	public function setValorTotal(float $value){
		$this->valor_total = $value;
	}

	//Status = Enum
	public function getStatus(): string{
		return $this->status;
	}

	public function setStatus(string $value){
		$this->status = $value;
	}

	//Forma de Pagamento = Enum
	public function getFormaPagamento(): string{
		return $this->forma_pagamento;
	}

	public function setFormaPagamento(string $value){
		$this->forma_pagamento = $value;
	}

	//Observações
	public function getObservacoes(): string{
		return $this->observacoes;
	}

	public function setObservacoes(string $value){
		$this->observacoes = $value;
	}

	//Tipo = Enum
	public function getTipo(): string{
		return $this->tipo;
	}

	public function setTipo(string $value){
		$this->tipo = $value;
	}

	//Endereço de entrega
	public function getEnderecoEntrega(): string{
		return $this->endereco_entrega;
	}

	public function setEnderecoEntrega(string $value){
		$this->endereco_entrega = $value;
	}

	//Taxa de Entrega
	public function getTaxaEntrega(): float{
		return $this->taxa_entrega;
	}

	public function setTaxaEntrega(float $value){
		$this->taxa_entrega = $value;
	}
}
