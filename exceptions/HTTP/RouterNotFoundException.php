<?php

namespace Core\Exception\Http;

use Core\Enum\StatusCodeHTTP;
use Exception\HTTP\HttpException;

class RouterNotFoundException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCodeHTTP::NOT_FOUND->value, $causes);
  }
}
