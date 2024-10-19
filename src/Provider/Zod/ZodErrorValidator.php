<?php

namespace App\Provider\Zod;

class ZodErrorValidator {

  private readonly string $message;

  /**
   * @var string[]
   */
  private readonly array $path;

  function __construct(string $message, string ...$paths) {
    $this->message = $message;
    $this->path = $paths;
  }

  function getError() {
    return [
      'message' => $this->message,
      'origin' => implode('.', $this->path),
    ];
  }
}
