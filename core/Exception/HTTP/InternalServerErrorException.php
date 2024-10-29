<?php

namespace Core\Exception\HTTP;

use Core\Enum\StatusCodeHTTP;
use Core\Exception\HTTP\HttpException;

class InternalServerErrorException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCodeHTTP::INTERNAL_SERVER_ERROR->value, $causes);
  }
}
