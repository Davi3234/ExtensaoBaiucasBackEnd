<?php

namespace Provider\Zod\Schemas;

use Provider\Zod\ZodSchemaException;

/**
 * @extends parent<object|array<array-key, mixed>>
 */
class ZodObjectSchema extends ZodSchema {

  /**
   * @var ZodSchema[]
   */
  private array $fields = [];

  /**
   * @param array<string, ZodSchema> $fields
   */
  function __construct($fields = [], $attributes = null) {
    parent::__construct($attributes, 'object');

    foreach ($fields as $field => $zodSchema) {
      if (!$zodSchema instanceof ZodSchema)
        throw new ZodSchemaException("Field \"$field\" must be a instance of the \"ZodSchema\"");
    }

    $this->fields = $fields;
    $this->addTransformRule('parseResolveFieldsSchema');
  }

  /**
   * @param array<string, ZodSchema> $fields
   */
  function extendsObject(array $fields, array|null $attribute = null) {
    $fields = array_merge($this->fields, $fields);

    $schema = new static($fields, $attribute);

    $schema->stackRules = $this->stackRules;

    return $schema;
  }

  function toArray() {
    $this->addTransformRule('parseToArray');
    return $this;
  }

  #[\Override]
  protected function parseCoerce($value, $attributes) {
    if (!is_array($value))
      return;

    $this->value = (object) $value;
  }

  protected function parseResolveFieldsSchema($_, $attributes) {
    $valueRaw = [];

    foreach ($this->fields as $key => $zodSchema) {
      $value = $this->value->$key ?? null;

      $result = $zodSchema->parseSafe($value);

      if (!$result['errors'])
        $valueRaw[$key] = $result['data'];
      else {
        foreach ($result['errors'] as $error)
          $this->addError($error['message'], $attributes['origin'] ?? [$key . ($error['origin'] ? '.' . $error['origin'][0] : '')]);
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
