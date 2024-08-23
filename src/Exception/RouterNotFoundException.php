<?php

namespace App\Exception;

use StatusCode;

class RouterNotFoundException extends HttpException {

  function __construct($message) {
    parent::__construct($message, StatusCode::NOT_FOUND);
  }
}