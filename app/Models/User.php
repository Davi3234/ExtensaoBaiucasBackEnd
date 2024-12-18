<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;
use App\Enums\TipoUsuario;

#[Entity]
#[Table(name: 'users')]
class User extends Model
{

	#[Id]
	#[GeneratedValue]
	#[Column]
	private int $id;
	#[Column]
	private string $name;
	#[Column]
	private string $login;
	#[Column]
	private string $cpf;
	#[Column]
	private string $endereco;
	#[Column]
	private string $password;
	#[Column(options: ['default' => true])]
	private bool $active;
	#[Column(type: 'string', enumType: TipoUsuario::class)]
	private TipoUsuario $tipo;

	public function __construct($id = 0, $name = '', $login = '', $cpf = '',$endereco = '',$password = '', $active = true, $tipo = TipoUsuario::CLIENTE)
	{
		$this->id = $id;
		$this->name = $name;
		$this->login = $login;
		$this->cpf = $cpf;
		$this->endereco = $endereco;
		$this->password = $password;
		$this->active = $active;
		$this->tipo = $tipo;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $value)
	{
		$this->id = $value;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $value)
	{
		$this->name = $value;
	}

	public function getLogin(): string
	{
		return $this->login;
	}

	public function getTipo(): TipoUsuario
	{
		return $this->tipo;
	}

	public function setLogin(string $value)
	{
		$this->login = $value;
	}

	public function getCpf(): string
	{
		return $this->cpf;
	}
	public function setCpf(string $value)
	{
		$this->cpf = $value;
	}

	public function getEndereco(): string
	{
		return $this->endereco;
	}
	public function setEndereco(string $value)
	{
		$this->endereco = $value;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $value)
	{
		$this->password = $value;
	}

	public function getActive(): bool
	{
		return $this->active;
	}

	public function setActive(bool $value)
	{
		$this->active = $value;
	}

	public function setTipo(TipoUsuario $value)
	{
		$this->tipo = $value;
	}
}
