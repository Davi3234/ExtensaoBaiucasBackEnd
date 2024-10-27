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
	#[Column(options: ['default' => true])]
	private bool $active;

	public function __construct(array $args = []) {
		$this->id = 0;
		$this->name = '';
		$this->login = '';
		$this->password = '';
		$this->active = true;

		$this->povoaPropriedades($args);
	}

	#[\Override]
	function __load(array $raw) {
		$this->id = $raw['id'];
		$this->name = $raw['name'];
		$this->login = $raw['login'];
		$this->password = $raw['password'];
		$this->active = $raw['active'];
	}

	protected function povoaPropriedades(array $args = []) {
		foreach ($args as $prop => $value) {
			$this->$prop = $value;
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

	public function getActive(): bool {
		return $this->active;
	}

	public function setActive(bool $value) {
		$this->active = $value;
	}
}
