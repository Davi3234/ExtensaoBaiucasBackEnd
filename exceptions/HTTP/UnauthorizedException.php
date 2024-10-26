<?php

namespace Core\Exception\Http;

use Core\Enum\StatusCode;
use Exception\HTTP\HttpException;

class UnauthorizedException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::UNAUTHORIZED->value, $causes);
  }
}
