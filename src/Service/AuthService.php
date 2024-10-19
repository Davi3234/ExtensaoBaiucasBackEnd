<?php

namespace App\Service;

use App\Provider\Zod\Z;

class AuthService {

  function login(array $args) {
    $loginSchema = Z::object([
      'login' => Z::string(),
      'password' => Z::string(),
    ])->coerce();

    $dto = $loginSchema->parseNoSafe($args);

    return ['message' => 'Teste'];
  }
}
