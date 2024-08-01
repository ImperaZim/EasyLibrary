<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\elements;

use pocketmine\form\FormValidationException;

/**
* Class Slider
* @param imperazim\vendor\libform\elements
*/
class Slider extends ElementWithValue {

  /**
  * Slider constructor.
  *
  * @param string $text The text of the slider element.
  * @param float $min The minimum value of the slider.
  * @param float $max The maximum value of the slider.
  * @param float $step The step value of the slider.
  * @param float|null $default The default value of the slider.
  * @param string|null $identifier The elemment identifier.
  */
  public function __construct(
    public string $text,
    public /*readonly*/ float $min,
    public /*readonly*/ float $max,
    public /*readonly*/ float $step = 1.0,
    public /*readonly*/ ?float $default = null,
    public ?string $identifier = null
  ) {
    parent::__construct($text, $default);
    $this->setIdentifier($identifier);
    if ($min > $max) {
      throw new \InvalidArgumentException("Slider min value should be less than max value");
    }
    if ($default === null) {
      $this->default = $min;
    } else {
      if ($default > $max || $default < $min) {
        throw new \InvalidArgumentException("Default must be in range $min ... $max");
      }
    }
    if ($step <= 0) {
      throw new \InvalidArgumentException("Step must be greater than zero");
    }
  }

  /**
  * Gets the type of the element.
  * @return string The type of the element.
  */
  protected function getType(): string {
    return "slider";
  }

  /**
  * Validates the value for the slider element.
  * @param mixed $value The value to validate.
  * @throws FormValidationException if the value is not a float or an int, or if it's out of bounds.
  */
  protected function validateValue(mixed $value): void {
    if (!is_float($value) && !is_int($value)) {
      throw new FormValidationException("Expected float or int, got " . gettype($value));
    }
    if ($value < $this->min || $value > $this->max) {
      throw new FormValidationException("Value $value is out of bounds (min $this->min, max $this->max)");
    }
  }

  /**
  * Serializes specific element data to an array.
  * @return array<string, mixed> The serialized specific element data.
  */
  protected function serializeElementData(): array {
    return [
      "min" => $this->min,
      "max" => $this->max,
      "step" => $this->step,
      "default" => $this->default,
    ];
  }
}