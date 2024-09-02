<?php

namespace App\Exception;

use App\Enum\StatusCode;

class BadRequestException extends HttpException {

  function __construct($message, array ...$causes) {
    parent::__construct($message, StatusCode::BAD_REQUEST->value, ...$causes);
  }
}
