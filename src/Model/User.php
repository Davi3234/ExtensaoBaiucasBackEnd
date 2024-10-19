<?php

namespace App\Model;

use App\Common\Model;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class User extends Model {
	#[Id]
	#[GeneratedValue]
	#[Column]
	public int $id;
	#[Column]
	private string $name;
	#[Column]
	private string $login;

	#[\Override]
	protected function __load(array $raw) {
		$this->id = $raw['id'];
		$this->name = $raw['name'];
		$this->login = $raw['login'];
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
}
