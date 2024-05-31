<?php

declare(strict_types = 1);

namespace internal\libform\elements;

use pocketmine\form\FormValidationException;

/**
* Class Toggle
* @param internal\libform\elements
*/
class Toggle extends ElementWithValue {

  /**
  * Toggle constructor.
  * @param string $text The text of the toggle element.
  * @param bool $default The default value of the toggle.
  * @param string|null $identifier The elemment identifier.
  */
  public function __construct(
    public string $text,
    public /*readonly*/ bool $default = false,
    public ?string $identifier = null
  ) {
    parent::__construct($text);
    $this->setIdentifier($identifier);
  }

  /**
  * Checks if the value has changed from the default.
  * @return bool True if the value has changed, false otherwise.
  */
  public function hasChanged(): bool {
    return $this->default !== $this->getValue();
    }

    /**
    * Gets the type of the element.
    * @return string The type of the element.
    */
    protected function getType(): string {
      return 'toggle';
    }

    /**
    * Validates the value for the toggle element.
    * @param mixed $value The value to validate.
    * @throws FormValidationException if the value is not a boolean.
    */
    protected function validateValue(mixed $value): void {
      if (!is_bool($value)) {
        throw new FormValidationException('Expected bool, got ' . gettype($value));
      }
    }

    /**
    * Serializes specific element data to an array.
    * @return array<string, mixed> The serialized specific element data.
    */
    protected function serializeElementData(): array {
      return [
        'default' => $this->default,
    ];
  }
}