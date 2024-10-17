<?php

namespace App\Exception\Http;

use App\Enum\StatusCode;
use App\Exception\Http\HttpException;

class InternalServerErrorException extends HttpException {

  function __construct(string $message, array $causes = []) {
    parent::__construct($message, StatusCode::INTERNAL_SERVER_ERROR->value, $causes);
  }
}
