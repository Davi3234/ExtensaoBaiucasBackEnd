<?php

namespace App\Exception;

class HttpException extends Exception {
  private $statusCode;

  function __construct($message, $statusCode, array $causes = []) {
    parent::__construct($message, $causes);

    $this->statusCode = $statusCode;
  }

  function getStatusCode() {
    return $this->statusCode;
  }
}
