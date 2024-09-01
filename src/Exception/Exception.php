<?php

namespace App\Exception;

class Exception extends \Exception {

  /**
   * @var array{message: string, causes: array{message: string, origin: ?string}}[]
   */
  private array $causes = [];

  /**
   * @param string $message
   * @param array{message: string, causes: array{message: string, origin: ?string}}[] $causes
   */
  function __construct(
    protected $message = "",
    array ...$causes
  ) {
    $this->causes = $causes;
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
