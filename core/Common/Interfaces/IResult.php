<?php

namespace Core\Common\Interfaces;

interface IResult {
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
