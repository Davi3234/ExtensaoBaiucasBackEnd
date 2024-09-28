<?php

namespace App\Model;

use App\Common\Model;

class User extends Model {
  public int $id;
  private string $name;
  private string $login;

  protected function _load(array $raw) {
    $this->id = $raw['id'];
    $this->name = $raw['name'];
    $this->login = $raw['login'];
  }

	public function getId() : int {
		return $this->id;
	}

	public function setId(int $value) {
		$this->id = $value;
	}

	public function getName() : string {
		return $this->name;
	}

	public function setName(string $value) {
		$this->name = $value;
	}

	public function getLogin() : string {
		return $this->login;
	}

	public function setLogin(string $value) {
		$this->login = $value;
	}
}