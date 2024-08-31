<?php

namespace App\Exception;

use App\Enum\StatusCode;

class NotFoundException extends HttpException {

  function __construct($message) {
    parent::__construct($message, StatusCode::NOT_FOUND->value);
  }
}
