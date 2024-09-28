<?php

namespace App\Exception\Http;

use App\Exception\RuntimeException;

class HttpException extends RuntimeException {
  private readonly int $statusCode;

  function __construct(string $message, int $statusCode, array $causes = []) {
    parent::__construct($message, $causes);

    $this->statusCode = $statusCode;
  }

  function getStatusCode() {
    return $this->statusCode;
  }
}
