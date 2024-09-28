<?php

namespace App\Provider\Zod;

/**
 * @extends parent<object|array<array-key, mixed>>
 */
class ZodObjectSchema extends ZodSchema {

  /**
   * @var ZodSchema[]
   */
  private array $fields = [];

  function __construct($fields = [], $attributes = null) {
    parent::__construct($attributes, 'object');

    foreach ($fields as $field => $zodSchema) {
      if (!$zodSchema instanceof ZodSchema)
        throw new ZodSchemaException("Field \"$field\" must be a instance of the \"ZodSchema\"");
    }

    $this->fields = $fields;
    $this->addTransformRule('parseResolveFieldsSchema');
  }

  function extendsObject($fields, $attribute = null) {
    $fields = array_merge($this->fields, $fields);

    $schema = new self($fields, $attribute);

    $schema->stackRules = $this->stackRules;

    return $schema;
  }

  function toArray() {
    $this->addTransformRule('parseToArray');
    return $this;
  }

  protected function parseCoerce($value, $attributes) {
    if (!is_array($value))
      return;

    $this->value = (object) $value;
  }

  protected function parseResolveFieldsSchema() {
    $valueRaw = [];

    foreach ($this->fields as $key => $zodSchema) {
      $value = $this->value->$key ?? null;

      $result = $zodSchema->parseSafe($value);

      if (!isset($result['errors']))
        $valueRaw[$key] = $result['data'];
      else {
        foreach ($result['errors'] as $error)
          $this->addError(new ZodErrorValidator($error['message'], $key . ($error['path'] ? '.' . $error['path'][0] : '')));
      }
    }

    $this->value = (object) $valueRaw;
  }

  protected function parseToArray($array) {
    $this->value = (array) $array;
  }

  protected function getShapes() {
    return $this->fields;
  }
}
