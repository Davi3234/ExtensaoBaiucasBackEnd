<?php

namespace Core\Exception\Http;

use Core\Enum\StatusCode;
use Exception\HTTP\HttpException;

class RouterNotFoundException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::NOT_FOUND->value, $causes);
  }
}
