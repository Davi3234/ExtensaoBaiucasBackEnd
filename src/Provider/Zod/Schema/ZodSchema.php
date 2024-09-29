<?php

namespace App\Provider\Zod\Schema;

use App\Provider\Zod\ZodErrorValidator;
use App\Provider\Zod\ZodParseException;

/**
 * @Template TSchemaType
 */
abstract class ZodSchema {

  /**
   * @var array{
   * DEFAULT: array{string: array{parser: callable, attributes: string[]}}, 
   * TRANSFORMINITIAL: array{string: array{parser: callable, attributes: string[]}}, 
   * TYPEVALIDATE: array{string: array{parser: callable, attributes: string[]}}, 
   * TRANSFORM: array{string: array{parser: callable, attributes: string[]}}, 
   * REFINERULE: array{string: array{parser: callable, attributes: string[]}}, 
   * REFINEEXTRA: array{string: array{parser: callable, attributes: string[]}}
   * }
   */
  protected array $stackRules = [
    'DEFAULT' => [],
    'TRANSFORMINITIAL' => [],
    'TYPEVALIDATE' => [],
    'REFINERULE' => [],
    'TRANSFORM' => [],
    'REFINEEXTRA' => [],
    'TRANSFORMEXTRA' => [],
  ];

  /**
   * @var ZodErrorValidator[]
   */
  protected $errors = [];

  private bool $isStop = false;
  protected $value = null;
  protected string $type;
  protected $_defaultValue = null;
  protected bool $isOptional = false;
  protected bool $isCoerce = false;

  function __construct(array $attributes, string $type) {
    $this->type = $type;

    $this->addTypeValidateRule('parseRequired', $attributes);
    $this->addTypeValidateRule('parseType', $attributes);
  }

  function __invoke($value) {
    return $this->parseNoSafe($value);
  }

  /**
   * @return TSchemaType
   */
  function parseNoSafe($value) {
    $response = $this->parseSafe($value);

    if ($response['errors']) {
      throw new ZodParseException('Invalid data', $response['errors']);
    }

    return $response['data'];
  }

  /**
   * @return array{data: ?TSchemaType, errors: ?array<string|int, array{message: mixed, path: mixed}>}
   */
  function parseSafe($value): array {
    $this->setup($value);
    $this->resolveStack();
    $response = $this->getParseResult();
    $this->clear();

    return $response;
  }

  protected function resolveStack() {
    $stackOrder = ['DEFAULT', 'TRANSFORMINITIAL', 'TYPEVALIDATE', 'TRANSFORM', 'REFINERULE', 'REFINEEXTRA', 'TRANSFORMEXTRA'];

    foreach ($stackOrder as $stack) {
      $this->resolveStackType($stack);

      if ($this->isStop)
        return;
    }
  }

  protected function resolveStackType(string $typeStack) {
    foreach ($this->stackRules[$typeStack] as $rule) {
      $response = $this->resolveValidator($typeStack, $rule['parser'], $rule['attributes']);

      $this->resolveResultHandleValidator($response, $rule['attributes']);

      if ($this->isStop)
        return;
    }
  }

  protected function resolveValidator(string $typeStack, string|callable $parser, array $attributes) {
    return $this->resolveHandleValidator($typeStack, $parser, $attributes);
  }

  protected function resolveHandleValidator(string $typeStack, string|callable $parser, array $attributes) {
    $response = $this->resolveHandle($parser, $attributes);

    if (!$response)
      return null;

    if (($response === false && $typeStack == 'REFINEEXTRA') || $response['message'])
      return $response;

    return null;
  }

  protected function resolveHandle(string|callable $parser, array $attributes) {
    if (is_string($parser) && method_exists($this, $parser))
      $response = $this->$parser($this->value, $attributes);
    else if (is_callable($parser))
      $response = $parser($this->value, $attributes);

    return $response;
  }

  protected function resolveResultHandleValidator($response, array $attributes) {
    if ($response === false)
      $this->addError(new ZodErrorValidator($attributes['message'] ?? 'Value invalid'));
    else if ($response && isset($response['message']))
      $this->addError(new ZodErrorValidator($response['message']));
  }

  protected function setup($value = null) {
    $this->errors = [];
    $this->value = $value;
  }

  protected function clear() {
    $this->value = null;
    $this->errors = [];
  }

  function optional() {
    $this->isOptional = true;
    return $this;
  }

  function coerce() {
    $this->isCoerce = true;
    $this->addTransformInitialRule('parseCoerce');
    return $this;
  }

  function defaultValue(callable|int|float|string|bool $value) {
    $this->_defaultValue = $value;
    $this->setDefaultRule('parseDefault');
    return $this;
  }

  function refine(string|callable $callable, array|string $attributes = null) {
    $this->addRefineExtraRule($callable, $attributes);
    return $this;
  }

  function transform(string|callable $callable, array|string $attributes = null) {
    $this->addTransformExtraRule(function () use ($callable, $attributes) {
      $this->value = $this->resolveHandle($callable, $attributes);
    }, $attributes);
    return $this;
  }

  protected function refineRule(string|callable $callable, array|string $attributes = null) {
    $this->addRefineRule($callable, $attributes);
    return $this;
  }

  abstract protected function parseCoerce($value, array $attributes);

  protected function parseRequired($value, $attributes) {
    if (!$this->isValueEmpty())
      return;

    if (!$this->isOptional)
      $this->addError(new ZodErrorValidator('Value is required'));

    $this->stop();
  }

  protected function parseType($value, $attributes) {
    if ($this->isValueEmpty()) {
      if ($this->isOptional)
        $this->stop();

      return;
    }

    if ($this->isValueSameType())
      return;

    $type = gettype($value);

    $this->addError(new ZodErrorValidator($attributes['invalidType'] ?? "Expect an \"$this->type\" received \"$type\""));
    $this->stop();
  }

  protected function parseDefault($value, array $attributes) {
    if (!$this->isValueEmpty())
      return;

    $defaultValue = $this->_defaultValue;

    if (is_callable($defaultValue)) {
      $this->value = $defaultValue();
    } else {
      $this->value = $this->_defaultValue;
    }
  }

  protected function addError(ZodErrorValidator $message) {
    $this->errors[] = $message;
  }

  /**
   * @return array{data: ?TSchemaType, errors: ?array<string|int, array{message: mixed, path: mixed}>}
   */
  protected function getParseResult() {
    $value = $this->value;
    $errors = $this->errors;

    return [
      'data' => !$errors ? $value : null,
      'errors' => array_map(function ($error) {
        return $error->getError();
      }, $errors),
    ];
  }

  protected function isValueEmpty() {
    return is_null($this->value);
  }

  protected function isValueSameType() {
    return gettype($this->value) == $this->type;
  }

  protected function stop() {
    $this->isStop = true;
  }

  protected function setDefaultRule(string $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('DEFAULT', $parserRule, $attributes, true);
  }

  protected function addTransformInitialRule(string $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORMINITIAL', $parserRule, $attributes);
  }

  protected function addTypeValidateRule(string $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('TYPEVALIDATE', $parserRule, $attributes);
  }

  protected function addTransformRule(string $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORM', $parserRule, $attributes);
  }

  protected function addRefineRule(string $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('REFINERULE', $parserRule, $attributes);
  }

  protected function addRefineExtraRule(string $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('REFINEEXTRA', $parserRule, $attributes);
  }

  protected function addTransformExtraRule(string|callable $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORMEXTRA', $parserRule, $attributes);
  }

  private function addRuleValidatorInStack(string $key, string|callable $parserRule, string|array $attributes = null, bool $uniqueRule = false) {
    if (is_string($attributes))
      $attributes = ['message' => $attributes];

    if (!$attributes) {
      $attributes = [];
    }

    $raw = [
      'parser' => $parserRule,
      'attributes' => $attributes,
    ];

    if (!$uniqueRule)
      $this->stackRules[$key][] = $raw;
    else
      $this->stackRules[$key] = [$raw];
  }
}
