<?php

namespace Core\Exception;

class CriticalException extends Exception {

  function __construct(string $message = "", array $causes = []) {
    parent::__construct($message, $causes);
  }
}
