<?php

namespace Core\Exception\Http;

use Core\Enum\StatusCode;
use Exception\HTTP\HttpException;

class InternalServerErrorException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::INTERNAL_SERVER_ERROR->value, $causes);
  }
}
