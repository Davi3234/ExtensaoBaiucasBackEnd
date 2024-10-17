<?php

namespace App\Exception\Http;

use App\Enum\StatusCode;
use App\Exception\Http\HttpException;

class BadRequestException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::BAD_REQUEST->value, $causes);
  }
}
