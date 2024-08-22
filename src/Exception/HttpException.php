<?php

namespace App\Exception;

class HttpException extends \Exception {
  private $statusCode;

  function __construct($message, $statusCode) {
    parent::__construct($message);

    $this->statusCode = $statusCode;
  }

  function getStatusCode() {
    return $this->statusCode;
  }
}