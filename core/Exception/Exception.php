<?php

namespace Core\Exception;

use Core\Common\Result;

class Exception extends \Exception {

  /**
   * @param string $message
   * @param array{message: string, origin: ?string}[] $causes
   */
  function __construct(
    protected $message = "",
    protected array $causes = []
  ) {
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

  function getCauses() {
    return $this->causes;
  }

  function getCausesAsString() {
    $causes = array_map(function ($cause) {
      $message = $cause['message'];
      $origin = $cause['origin'];

      if ($origin) {
        $message = "$origin: $message";
      }

      return "$message";
    }, $this->causes);

    return '"' . implode('; ', $causes) . '"';
  }

  function __toString(): string {
    return "Message: $this->message\n"
      . "Causes: {$this->getCausesAsString()}\n"
      . "Trace: {$this->getTraceAsString()}";
  }
}
