<?php

namespace App\Exception;

use App\Enums\StatusCode;

class UnauthorizedException extends HttpException {

  function __construct($message) {
    parent::__construct($message, StatusCode::UNAUTHORIZED->value);
  }
}
