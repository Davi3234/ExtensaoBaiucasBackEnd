<?php

namespace Core\Exception\HTTP;

use Core\Enum\StatusCodeHTTP;
use Core\Exception\HTTP\HttpException;

class UnauthorizedException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCodeHTTP::UNAUTHORIZED->value, $causes);
  }
}
