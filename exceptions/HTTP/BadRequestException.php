<?php

namespace Exception\HTTP;

use Core\Enum\StatusCode;

class BadRequestException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::BAD_REQUEST->value, $causes);
  }
}
