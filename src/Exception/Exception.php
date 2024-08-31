<?php

namespace App\Exception;

class Exception extends \Exception {

  /**
   * @param string $message
   * @param array{message: string, origin: string[]} $causes
   */
  function __construct(
    protected $message = "",
    protected array $causes = []
  ) {
  }

  /**
   * @return array{message: string, origin: string[]}
   */
  function getCauses() {
    return $this->causes;
  }
}
