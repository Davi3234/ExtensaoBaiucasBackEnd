<?php

namespace App\Provider\Zod;

class ZodErrorValidator {

  private $message;
  private $path;

  function __construct($message, ...$paths) {
    $this->message = $message;
    $this->path = $paths;
  }

  function getError() {
    return [
      'message' => $this->message,
      'path' => $this->path,
    ];
  }
}