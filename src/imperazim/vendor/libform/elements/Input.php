<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\elements;

use pocketmine\form\FormValidationException;

/**
* Class Input
* @param imperazim\vendor\libform\elements
*/
class Input extends ElementWithValue {

  /**
  * Input constructor.
  * @param string $text The text of the input element.
  * @param string $placeholder The placeholder text for the input.
  * @param string $default The default text for the input.
  * @param string|null $identifier The elemment identifier.
  */
  public function __construct(
    public string $text,
    public /*readonly*/ string $placeholder,
    public /*readonly*/ string $default = "",
    public ?string $identifier = null
  ) {
    parent::__construct($text);
    $this->setIdentifier($identifier);
  }

  /**
  * Gets the type of the element.
  * @return string The type of the element.
  */
  protected function getType(): string {
    return "input";
  }

  /**
  * Validates the value for the input element.
  * @param mixed $value The value to validate.
  * @throws FormValidationException if the value is not a string.
  */
  protected function validateValue(mixed $value): void {
    if (!is_string($value)) {
      throw new FormValidationException("Expected string, got " . gettype($value));
    }
  }

  /**
  * Serializes specific element data to an array.
  * @return array<string, mixed> The serialized specific element data.
  */
  protected function serializeElementData(): array {
    return [
      "placeholder" => $this->placeholder,
      "default" => $this->default,
    ];
  }
}