<?php

namespace Provider\Zod;

class ZodErrorValidator {

  private readonly string $message;

  /**
   * @var string[]
   */
  private readonly array $origin;

  function __construct(string $message, string ...$origins) {
    $this->message = $message;
    $this->origin = $origins;
  }

  function getError() {
    return [
      'message' => $this->message,
      'origin' => implode('.', $this->origin),
    ];
  }
}
