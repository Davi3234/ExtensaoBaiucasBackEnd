<?php

namespace Core\Exception;

use Core\Common\Result;

class Exception extends \Exception {

  /** @var array{message: string, origin: string[]}[] */
  protected array $causes;


  /**
   * @param string $message
   * @param array{message: string, origin: ?(string|string[])}[] $causes
   */
  function __construct(
    protected $message = "",
    array $causes = []
  ) {
    $this->causes = array_map(function ($cause) {
      return [
        'message' => $cause['message'],
        'origin' => is_array($cause['origin']) ? $cause['origin'] : ($cause['origin'] ? [$cause['origin']] : []),
      ];
    }, $causes);
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

  function getCausesFromOrigin(string $origin) {
    return array_filter($this->causes, function ($cause) use ($origin) {
      return in_array($origin, $cause['origin']);
    });
  }

  function getCausesAsString() {
    $causes = array_map(function ($cause) {
      $message = $cause['message'];
      $origin = $cause['origin'];

      if ($origin) {
        $message = "$origin: $message";
      }

      return trim($message);
    }, $this->causes);

    return '"' . implode('; ', $causes) . '"';
  }

  function __toString(): string {
    return "Message: $this->message\n"
      . "Causes: {$this->getCausesAsString()}\n"
      . "Trace: {$this->getTraceAsString()}";
  }
}
