<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Common\Model;

#[Entity]
#[Table(name: 'users')]
class User extends Model {
	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id;
	#[Column]
	private string $name;
	#[Column]
	private string $login;
	#[Column]
	private string $password;

	public function __construct(array $args = []) {
		$this->id = 0;
		$this->name = '';
		$this->login = '';
		$this->password = '';

		$this->povoaPropriedades($args);
	}

	#[\Override]
	function __load(array $raw) {
		$this->id = $raw['id'];
		$this->name = $raw['name'];
		$this->login = $raw['login'];
		$this->password = $raw['password'];
	}

	protected function povoaPropriedades(array $args = []) {
		foreach ($args as $nomeprop => $prop) {
			$this->$nomeprop = $prop;
		}
	}

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $value) {
		$this->id = $value;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $value) {
		$this->name = $value;
	}

	public function getLogin(): string {
		return $this->login;
	}

	public function setLogin(string $value) {
		$this->login = $value;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword(string $value) {
		$this->password = $value;
	}
}
