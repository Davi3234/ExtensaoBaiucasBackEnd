<?php

namespace Core\Exception\Http;

use Core\Enum\StatusCodeHTTP;
use Exception\HTTP\HttpException;

class UnauthorizedException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCodeHTTP::UNAUTHORIZED->value, $causes);
  }
}
