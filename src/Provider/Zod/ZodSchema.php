<?php

namespace App\Provider\Zod;

abstract class ZodSchema {

  /**
   * @var array{
   * DEFAULT: array{string: array{parser: callable, attributes: array<string>}}, 
   * TRANSFORMINITIAL: array{string: array{parser: callable, attributes: array<string>}}, 
   * TYPEVALIDATE: array{string: array{parser: callable, attributes: array<string>}}, 
   * TRANSFORM: array{string: array{parser: callable, attributes: array<string>}}, 
   * REFINERULE: array{string: array{parser: callable, attributes: array<string>}}, 
   * REFINEEXTRA: array{string: array{parser: callable, attributes: array<string>}}
   * }
   */
  protected $stackRules = [
    'DEFAULT' => [],
    'TRANSFORMINITIAL' => [],
    'TYPEVALIDATE' => [],
    'REFINERULE' => [],
    'TRANSFORM' => [],
    'REFINEEXTRA' => [],
    'TRANSFORMEXTRA' => [],
  ];
  /**
   * @var array<ZodErrorValidator>
   */
  protected $errors = [];

  protected $value = null;
  protected $type;
  private $isStop = false;
  protected $defaultValue = null;
  protected $isOptional = false;
  protected $isCoerce = false;

  function __construct($attributes, $type) {
    $this->type = $type;

    $this->addTypeValidateRule('parseRequired', $attributes);
    $this->addTypeValidateRule('parseType', $attributes);
  }

  function parse($value): array|object {
    $this->setup($value);
    $this->resolveStack();
    $response = $this->getParseResult();
    $this->clear();

    return $response;
  }

  protected function resolveStack() {
    $stackOrder = ['DEFAULT', 'TRANSFORMINITIAL', 'TYPEVALIDATE', 'TRANSFORM', 'REFINERULE', 'REFINEEXTRA', 'TRANSFORMEXTRA'];

    foreach($stackOrder as $stack) {
      $this->resolveStackType($stack);

      if ($this->isStop)
        return;
    }
  }

  protected function resolveStackType($typeStack) {
    foreach ($this->stackRules[$typeStack] as $key => $rule) {
      $response = $this->resolveValidator($typeStack, $rule['parser'], $rule['attributes']);

      $this->resolveResultHandleValidator($response, $rule['attributes']);

      if ($this->isStop)
        return;
    }
  }

  protected function resolveValidator($typeStack, $parser, $attributes) {
    return $this->resolveHandleValidator($typeStack, $parser, $attributes);
  }

  protected function resolveHandleValidator($typeStack, $parser, $attributes) {
    $response = $this->resolveHandle($parser, $attributes);

    if (!$response)
      return null;

    if (($response === false && $typeStack == 'REFINEEXTRA') || $response['message'])
      return $response;

    return null;
  }

  protected function resolveHandle($parser, $attributes) {
    if (is_string($parser) && method_exists($this, $parser))
      $response = $this->$parser($this->value, $attributes);
    else if (is_callable($parser))
      $response = $parser($this->value, $attributes);

    return $response;
  }

  protected function resolveResultHandleValidator($response, $attributes) {
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

  function defaultValue($value) {
    $this->defaultValue = $value;
    $this->setDefaultRule('parseDefault');
    return $this;
  }

  function refine($callable, $attributes = null) {
    $this->addRefineExtraRule($callable, $attributes);
    return $this;
  }

  function transform($callable, $attributes = null) {
    $this->addTransformExtraRule(function () use ($callable, $attributes) {
      $this->value = $this->resolveHandle($callable, $attributes);
    }, $attributes);
    return $this;
  }

  protected function refineRule($callable, $attributes = null) {
    $this->addRefineRule($callable, $attributes);
    return $this;
  }

  abstract protected function parseCoerce($value, $attributes);

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

  protected function parseDefault($value, $attributes) {
    if (!$this->isValueEmpty())
      return;

    $this->value = $this->defaultValue;
  }

  protected function addError($message) {
    $this->errors[] = $message;
  }

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

  protected function setDefaultRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('DEFAULT', $parserRule, $attributes, true);
  }

  protected function addTransformInitialRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORMINITIAL', $parserRule, $attributes);
  }

  protected function addTypeValidateRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('TYPEVALIDATE', $parserRule, $attributes);
  }

  protected function addTransformRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORM', $parserRule, $attributes);
  }

  protected function addRefineRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('REFINERULE', $parserRule, $attributes);
  }

  protected function addRefineExtraRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('REFINEEXTRA', $parserRule, $attributes);
  }

  protected function addTransformExtraRule($parserRule, $attributes = null) {
    $this->addRuleValidatorInStack('TRANSFORMEXTRA', $parserRule, $attributes);
  }

  private function addRuleValidatorInStack($key, $parserRule, $attributes = null, $uniqueRule = false) {
    if (is_string($attributes))
      $attributes = ['message' => $attributes];

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
