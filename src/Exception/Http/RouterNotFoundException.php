<?php

namespace App\Exception\Http;

use App\Enum\StatusCode;
use App\RuntimeException\Http\HttpException;

class RouterNotFoundException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::NOT_FOUND->value, $causes);
  }
}
