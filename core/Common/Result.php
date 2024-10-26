<?php

namespace Core\Common;

use Core\Exception\CriticalException;
use Core\Common\Interfaces\IResult;

class Result implements IResult {
  private readonly bool $ok;
  private readonly int $status;
  private readonly array|object|int|float|bool|string|null $value;

  /**
   * @var array{message: string, causes: array{message: string, origin: ?string}}[]|null
   */
  private ?array $error = null;

  private function __construct(bool $ok, int $status, $value = null, $error = null) {
    if ($ok) {
      if ($status >= 400)
        throw new CriticalException(
          "It is not possible to define a status code greater than or equal to 400 when the result is success",
          [
            ['message' => "Status code received was \"$status\"", 'origin' => 'status']
          ]
        );
    } else if ($status < 400)
      throw new CriticalException(
        "It is not possible to set a status code lower than 400 when the result is failure",
        [
          ['message' => "Status code received was \"$status\"", 'origin' => 'status']
        ]
      );

    $this->ok = $ok;
    $this->status = $status;
    $this->value = $value;
    $this->error = $error;
  }

  static function success(array|object|int|float|bool|string|null $value, int $status = 200): Result {
    return new Result(true, $status, $value);
  }

  /**
   * @param array{message: string, causes: array{message: string, origin: ?string}}[] $causes
   * @param int $status
   */
  static function failure(array $error, int $status = 400): Result {
    $message = $error['message'] ?? '';
    $causes = $error['causes'] ?? [];

    return new Result(false, $status, null, ['message' => $message, 'causes' => $causes]);
  }

  static function inherit(bool $ok = true, int $status = 200, array|object|int|float|bool|string|null $value = null, $error = null) {
    return new Result($ok, $status, $value, $error);
  }

  #[\Override]
  function getResponse() {
    if ($this->isSuccess())
      return $this->getValue();

    return $this->getError();
  }

  #[\Override]
  function isSuccess(): bool {
    return $this->ok;
  }

  #[\Override]
  function getValue(): array|object|int|float|bool|string|null {
    return $this->value;
  }

  #[\Override]
  function getError(): ?array {
    return $this->error;
  }

  #[\Override]
  function getStatus(): int {
    return $this->status;
  }

  #[\Override]
  function getResult(): array {
    return ['ok' => $this->ok, 'status' => $this->status, 'value' => $this->value, 'error' => $this->error];
  }

  function __toString() {
    $ok = str_bool($this->ok);
    $status = $this->status;
    $value = json_encode($this->value);
    $error = json_encode($this->error);

    return "Result{ok: $ok, status: $status, value: $value, error: $error}";
  }
}
