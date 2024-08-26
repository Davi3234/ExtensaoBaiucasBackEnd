<?php

namespace App\Exception;

class NotFoundException extends HttpException {

  function __construct($message) {
    parent::__construct($message, 404);
  }
}
