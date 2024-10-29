<?php

namespace Core\Exception;

class RuntimeException extends Exception {

  function __construct(string $message = "", array $causes = []) {
    parent::__construct($message, $causes);
  }
}
