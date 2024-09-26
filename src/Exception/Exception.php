<?php

namespace App\Exception;

class Exception extends \Exception {

  /**
   * @param string $message
   * @param array{message: string, causes: array{message: string, origin: ?string}}[] $causes
   */
  function __construct(
    protected $message = "",
    protected array $causes = []
  ) {
  }

  function getCauses() {
    return $this->causes;
  }

  function getInfoError() {
    return [
      'message' => $this->message,
      'causes' => $this->causes,
    ];
  }
}
