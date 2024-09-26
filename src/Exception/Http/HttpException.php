<?php

namespace App\RuntimeException\Http;

class HttpException extends RuntimeException {
  private $statusCode;

  function __construct($message, $statusCode, array $causes = []) {
    parent::__construct($message, $causes);

    $this->statusCode = $statusCode;
  }

  function getStatusCode() {
    return $this->statusCode;
  }
}
