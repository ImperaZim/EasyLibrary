<?php

declare(strict_types = 1);

namespace internal\libform\elements;

use pocketmine\form\FormValidationException;

/**
* Class Selector
* @param internal\libform\elements
*/
abstract class Selector extends ElementWithValue {

  /**
  * Selector constructor.
  * @param string $text The text of the selector element.
  * @param array<int, string> $options The list of options for the selector.
  * @param int $default The default selected index.
  */
  public function __construct(
    public string $text,
    public array $options, int $default = 0
  ) {
    parent::__construct($text, $default);
  }

  /**
  * Gets the index of the selected option.
  * @return int The index of the selected option.
  */
  public function getSelectedIndex(): int {
    return $this->getValue();
  }

  /**
  * Gets the selected option text.
  * @return string The text of the selected option.
  */
  public function getSelectedOption(): string {
    return $this->options[$this->getValue()];
  }

  /**
  * Validates the value for the selector.
  * @param mixed $value The value to validate.
  * @throws FormValidationException if the value is not an integer or does not exist in options.
  */
  protected function validateValue(mixed $value): void {
    if (!is_int($value)) {
      throw new FormValidationException("Expected int, got " . gettype($value));
    }
    if (!isset($this->options[$value])) {
      throw new FormValidationException("Option $value does not exist");
    }
  }

}