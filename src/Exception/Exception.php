<?php

namespace App\Exception;

use App\Core\Components\Result;

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

  function toResult() {
    return Result::failure($this->getInfoError());
  }

  function getInfoError() {
    return [
      'message' => $this->message,
      'causes' => $this->causes,
    ];
  }
}
