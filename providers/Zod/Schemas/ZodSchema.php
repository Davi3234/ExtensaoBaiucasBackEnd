<?php

namespace Provider\Zod\Schemas;

use Provider\Zod\ZodErrorValidator;
use Provider\Zod\ZodParseException;

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

  /**
   * @var array|object|callable|int|float|string|bool|null
   */
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
   * @return array{data: ?TSchemaType, errors: ?array<string|int, array{message: mixed, origin: mixed}>}
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

    if (($response === false && $typeStack == 'REFINEEXTRA') || $response['message'])
      return $response;

    if (!$response)
      return null;

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
      $this->addError($attributes['message'] ?? 'Value invalid', $attributes['origin'] ? [$attributes['origin']] : []);
    else if ($response && isset($response['message']))
      $this->addError($response['message'], $attributes['origin'] ? [$attributes['origin']] : []);
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

  function defaultValue(array|object|callable|int|float|string|bool $value) {
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
      $this->value = $this->resolveHandle($callable, $attributes ?? []);
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
      $this->addError('Value is required', $attributes['origin'] ?? []);

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

    $this->addError($attributes['invalidType'] ?? "Expect an \"$this->type\" received \"$type\"", $attributes['origin'] ?? []);
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

  /**
   * @param string $message
   * @param string[] $origins
   */
  protected function addError(string $message, array $origins = []) {
    $this->errors[] = new ZodErrorValidator($message, ...$origins);
  }

  /**
   * @return array{data: ?TSchemaType, errors: ?array<string|int, array{message: mixed, origin: mixed}>}
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

  protected function addTypeValidateRule(string|callable $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('TYPEVALIDATE', $parserRule, $attributes);
  }

  protected function addTransformRule(string|callable $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORM', $parserRule, $attributes);
  }

  protected function addRefineRule(string|callable $parserRule, array|string $attributes = null) {
    $this->addRuleValidatorInStack('REFINERULE', $parserRule, $attributes);
  }

  protected function addRefineExtraRule(string|callable $parserRule, array|string $attributes = null) {
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
