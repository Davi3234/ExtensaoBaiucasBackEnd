<?php

namespace App\Exception;

class InternalServerErrorException extends HttpException {

  function __construct($message) {
    parent::__construct($message, 500);
  }
}