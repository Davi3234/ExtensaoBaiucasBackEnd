<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;

#[Entity]
#[Table(name: 'pedidos')]
class Pedido extends Model {

	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id_pedido;
	
    #[Column]
	public date $data_pedido;
	
    #[Column]
	public int $id_cliente; //ver como pegar o id do cliente diretamente se é por aqui ou em outro lugar
	
    #[Column]
    public doubleval $valor_total;
	
    #[Column]
    enum status: string {
        case EM PREPARO = 'PREP';
        case CONCLUÍDO  = 'CONC';
        case EM ENTREGA = 'ENTR';
        case CANCELADO  = 'CANC';
    } $status;
	
    #[Column]
	enum forma_pagamento: string {
        case CARTÃO   = 'CAR';
        case DINHEIRO = 'DIN';
        case PIX      = 'PIX';
    } $forma_pagamento;

    #[Column]
    public string $observacoes;

    #[Column]
	enum tipo: string {
        case NO LOCAL = 'LOC';
        case DELIVERY = 'DEL';
    } $tipo;

    #[Column]
    public string $endereco_entrega;

    #[Column]
    public doubleval $taxa_entrega;

	public function __construct(array $args = []) {
		$this->id_pedido        = 0;
		$this->data_pedido      = null;
		$this->id_cliente       = 0;
		$this->valor_total      = 0;
		$this->status           = '';
        $this->forma_pagamento  = '';
        $this->observacoes      = '';
        $this->tipo             = '';
        $this->endereco_entrega = '';
        $this->taxa_entrega     = 0;

		$this->povoaPropriedades($args);
	}

	#[\Override]
	function __load(array $raw) {
		$this->id_pedido         = $raw['id_pedido'];
		$this->data_pedido       = $raw['data_pedido'];
		$this->id_cliente        = $raw['id_cliente'];
		$this->valor_total       = $raw['valor_total'];
        $this->status            = $raw['status'];
		$this->forma_pagamento   = $raw['forma_pagamento'];
        $this->observacoes       = $raw['observacoes'];
        $this->tipo              = $raw['tipo'];
        $this->endereco_entrega  = $raw['endereco_entrega'];
        $this->taxa_entrega      = $raw['taxa_entrega'];
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

    //Data do Pedido
	public function getDataPedido(): date {
		return $this->data_pedido;
	}

    public function setDataPedido(date $value) {
		$this->data_pedido = $value;
	}

    //Id do cliente
	public function getIdCliente(): int {
		return $this->id_cliente;
	}

    public function setIdCliente(int $value) {
		$this->id_cliente = $value;
	}

    //Valor Total Pedido
    public function getValorTotal(): doubleval {
		return $this->valor_total;
	}

	public function setValorTotal(doubleval $value) {
		$this->valor_total = $value;
	}

    //Status = Enum
    public function getStatus(): status {
		return $this->status;
	}

	public function setStatus(status $value) {
		$this->status = $value;
	}

    //Forma de Pagamento = Enum
    public function getFormaPagamento(): forma_pagamento {
		return $this->forma_pagamento;
	}

	public function setFormaPagamento(forma_pagamento $value) {
		$this->forma_pagamento = $value;
	}

    //Observações
    public function getObservacoes(): string {
		return $this->observacoes;
	}

	public function setObservacoes(string $value) {
		$this->observacoes = $value;
	}

    //Tipo = Enum
    public function getTipo(): tipo {
		return $this->tipo;
	}

	public function setTipo(tipo $value) {
		$this->tipo = $value;
	}

    //Endereço de entrega
    public function getEnderecoEntrega(): string {
		return $this->endereco_entrega;
	}

	public function setEnderecoEntrega(string $value) {
		$this->endereco_entrega = $value;
	}

    //Taxa de Entrega
    public function getTaxaEntrega(): doubleval {
		return $this->taxa_entrega;
	}

	public function setTaxaEntrega(doubleval $value) {
		$this->taxa_entrega = $value;
	}
}
