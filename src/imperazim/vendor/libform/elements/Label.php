<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\elements;

use pocketmine\form\FormValidationException;

/**
* Class Label
* @param imperazim\vendor\libform\elements
*/
class Label extends ElementWithValue {

  /**
  * Label constructor.
  * @param string $text The text of the label element.
  */
  public function __construct(
    public string $text
  ) {
    parent::__construct($text, null);
  }

  /**
  * Gets the type of the element.
  * @return string The type of the element.
  */
  protected function getType(): string {
    return "label";
  }

  /**
  * Validates the value for the label element.
  * @param mixed $value The value to validate.
  * @throws FormValidationException if the value is not null.
  */
  protected function validateValue(mixed $value): void {
    if (!is_null($value)) {
      throw new FormValidationException("Expected null, got " . gettype($value));
    }
  }

  /**
  * Serializes specific element data to an array.
  * @return array<string, mixed> The serialized specific element data.
  */
  protected function serializeElementData(): array {
    return [];
  }
}