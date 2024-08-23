<?php

namespace App\Exception;

class BadRequestException extends HttpException {

  function __construct($message) {
    parent::__construct($message, 400);
  }
}