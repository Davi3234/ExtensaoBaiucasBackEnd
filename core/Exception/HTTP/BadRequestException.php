<?php

namespace Core\Exception\HTTP;

use Core\Enum\StatusCodeHTTP;

class BadRequestException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCodeHTTP::BAD_REQUEST->value, $causes);
  }
}
