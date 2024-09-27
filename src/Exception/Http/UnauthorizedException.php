<?php

namespace App\Exception;

use App\Enum\StatusCode;
use App\RuntimeException\Http\HttpException;

class UnauthorizedException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::UNAUTHORIZED->value, $causes);
  }
}
