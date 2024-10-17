<?php

namespace App\Exception\Http;

use App\Core\Components\Result;
use App\Exception\RuntimeException;

class HttpException extends RuntimeException {

  function __construct(
    string $message,
    private readonly int $statusCode,
    array $causes = []
  ) {
    parent::__construct($message, $causes);
  }

  function getStatusCode() {
    return $this->statusCode;
  }

  #[\Override]
  function toResult() {
    return Result::failure($this->getInfoError(), $this->statusCode);
  }
}
