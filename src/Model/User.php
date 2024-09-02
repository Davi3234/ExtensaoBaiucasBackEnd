<?php

namespace App\Model;

use App\Common\Model;

class User extends Model {
  public int $id;
  private string $name;
  private string $login;

  static function _loadModel(array $raw) {
    $instance = new self;

    $instance->id = $raw['id'];
    $instance->name = $raw['name'];
    $instance->login = $raw['login'];

    return $instance;
  }

  function _load(array $raw) {
    $this->id = $raw['id'];
    $this->name = $raw['name'];
    $this->login = $raw['login'];
  }

  function getName() {
    return $this->name;
  }

  function getLogin() {
    return $this->login;
  }
}
