<?php

namespace App\Exception;

use App\Enum\StatusCode;

class UnauthorizedException extends HttpException {

  function __construct($message, array $causes = []) {
    parent::__construct($message, StatusCode::UNAUTHORIZED->value, $causes);
  }
}
