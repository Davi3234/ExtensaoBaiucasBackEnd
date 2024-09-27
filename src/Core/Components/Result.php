<?php

namespace App\Core\Components;

use App\Exception\Exception;

interface ResultModel {
  function getResponse();
  function isSuccess(): bool;
  function getValue(): mixed;

  /**
   * @return array{message: string, causes: array{message: string, origin: ?string}}[]|null
   */
  function getError(): ?array;
  function getStatus(): int;

  /**
   * @return array{ok: bool, status: int, value: mixed, error: array{message: string, causes: array{message: string, origin: null|string}}[]|null}
   */
  function getResult(): array;
}

class Result implements ResultModel {
  private bool $ok = true;
  private int $status = 200;
  private $value = null;

  /**
   * @var array{message: string, causes: array{message: string, origin: ?string}}[]|null
   */
  private ?array $error = null;

  private function __construct(bool $ok, int $status, $value = null, $error = null) {
    if ($ok) {
      if ($status >= 400)
        throw new Exception(
          "It is not possible to define a status code greater than or equal to 400 when the result is success",
          [['message' => "Status code received was \"$status\"", 'origin' => 'status']]
        );
    } else if ($status < 400)
      throw new Exception(
        "It is not possible to set a status code lower than 400 when the result is failure",
        [['message' => "Status code received was \"$status\"", 'origin' => 'status']]
      );

    $this->ok = $ok;
    $this->status = $status;
    $this->value = $value;
    $this->error = $error;
  }

  static function success($value, int $status = 200) {
    return new Result(true, $status, $value);
  }

  /**
   * @param array{message: string, causes: array{message: string, origin: ?string}}[] $causes
   * @param int $status
   * @return Result
   */
  static function failure(array $error, int $status = 400) {
    $message = $error['message'] ?? '';
    $causes = $error['causes'] ?? [];

    return new Result(false, $status, null, ['message' => $message, 'causes' => $causes,]);
  }

  static function inherit(bool $ok = true, int $status = 200, $value = null, $error = null) {
    return new Result($ok, $status, $value, $error);
  }

  function getResponse() {
    if ($this->isSuccess())
      return $this->getValue();

    return $this->getError();
  }

  function isSuccess(): bool {
    return $this->ok;
  }

  function getValue(): mixed {
    return $this->value;
  }

  function getError(): ?array {
    return $this->error;
  }

  function getStatus(): int {
    return $this->status;
  }

  function getResult(): array {
    return ['ok' => $this->ok, 'status' => $this->status, 'value' => $this->value, 'error' => $this->error];
  }
}
