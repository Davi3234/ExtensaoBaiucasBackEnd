<?php

namespace App\Exception;

use App\Enum\StatusCode;

class NotFoundException extends HttpException {

  function __construct($message, array ...$causes) {
    parent::__construct($message, StatusCode::NOT_FOUND->value, ...$causes);
  }
}
