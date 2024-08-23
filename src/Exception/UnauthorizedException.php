<?php

namespace App\Exception;

class UnauthorizedException extends HttpException {

  function __construct($message) {
    parent::__construct($message, 401);
  }
}
