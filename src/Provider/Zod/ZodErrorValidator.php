<?php

namespace App\Provider\Zod;

class ZodErrorValidator {

  private string $message;

  /**
   * @var string[]
   */
  private array $path;

  function __construct(string $message, string ...$paths) {
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
