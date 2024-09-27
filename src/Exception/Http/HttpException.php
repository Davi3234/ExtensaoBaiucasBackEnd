<?php

namespace App\RuntimeException\Http;

use App\Exception\RuntimeException;

class HttpException extends RuntimeException {
  private $statusCode;

  function __construct(string $message, int $statusCode, array $causes = []) {
    parent::__construct($message, $causes);

    $this->statusCode = $statusCode;
  }

  function getStatusCode() {
    return $this->statusCode;
  }
}
