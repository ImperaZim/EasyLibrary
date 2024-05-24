<?php

declare(strict_types = 1);

namespace internal\libform\elements;

use internal\libform\traits\IdentifiableElement;

/**
* Class Element
* @param internal\libform\elements
*/
abstract class Element implements \JsonSerializable {
  use IdentifiableElement;

  /**
  * Element constructor.
  * @param string $text The text of the element.
  */
  public function __construct(
    public /*readonly*/ string $text
  ) {}

  /**
  * Serializes the element to an array for JSON encoding.
  * @return array<string, mixed> The serialized data of the element.
  */
  final public function jsonSerialize(): array {
    $data = $this->serializeElementData();
    $data["type"] = $this->getType();
    $data["text"] = $this->text;
    return $data;
  }

  /**
  * Serializes specific element data to an array.
  * @return array<string, mixed> The serialized specific element data.
  */
  abstract protected function serializeElementData(): array;

  /**
  * Gets the type of the element.
  * @return string The type of the element.
  */
  abstract protected function getType(): string;

  /**
  * Validates the value for the element.
  * @param mixed $value The value to validate.
  */
  abstract protected function validateValue(mixed $value): void;

}