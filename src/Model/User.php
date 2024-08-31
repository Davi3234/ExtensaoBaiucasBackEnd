<?php

namespace App\Model;

use App\Common\Model;

class User extends Model {
  public int $id;
  private string $name;
  private string $login;

  function getName() {
    return $this->name;
  }

  function getLogin() {
    return $this->login;
  }
}
