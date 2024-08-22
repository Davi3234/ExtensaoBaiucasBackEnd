<?php

namespace App\Exception;

class RouterNotFoundException extends HttpException {

  function __construct($message) {
    parent::__construct($message, 404);
  }
}