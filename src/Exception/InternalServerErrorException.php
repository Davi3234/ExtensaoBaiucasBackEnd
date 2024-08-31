<?php

namespace App\Exception;

use App\Enum\StatusCode;

class InternalServerErrorException extends HttpException {

  function __construct($message) {
    parent::__construct($message, StatusCode::INTERNAL_SERVER_ERROR->value);
  }
}
