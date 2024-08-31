<?php

namespace App\Model;

class User {
  private $id;
  private $login;
  private $senha;

  function __get($propName) {
    return $this->data[$propName];
  }

  function __set($propName, $propValue) {
    $this->data[$propName] = $propValue;
  }
}
