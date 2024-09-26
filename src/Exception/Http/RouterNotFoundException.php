<?php

namespace App\Exception\Http;

use App\Enum\StatusCode;

class RouterNotFoundException extends HttpException {

  function __construct($message, array $causes = []) {
    parent::__construct($message, StatusCode::NOT_FOUND->value, $causes);
  }
}
