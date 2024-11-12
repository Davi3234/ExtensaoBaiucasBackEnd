<?php

namespace App\Models;

use App\Models\User;
use App\Enums\TipoUsuario;

class Administrador extends User {
  public function __construct($id = 0, $name = '', $login = '', $password = '', $active = true) {
    parent::__construct(
      id: $id,
      name: $name,
      login: $login,
      password: $password,
      active: $active,
      tipo: TipoUsuario::ADMNISTRADOR
    );
  }
}
