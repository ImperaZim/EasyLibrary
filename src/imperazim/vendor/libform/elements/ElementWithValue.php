<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\elements;

/**
* Class ElementWithValue
* @param imperazim\vendor\libform\elements
*/
abstract class ElementWithValue extends Element {

  /**
  * ElementWithValue constructor.
  * @param string $text The text of the element.
  * @param TValue|null $value The initial value of the element.
  */
  public function __construct(
    public string $text,
    protected mixed $value = null
  ) {
    parent::__construct($text);
  }

  /**
  * Gets the value of the element.
  * @return TValue The value of the element.
  * @throws \UnexpectedValueException if the value is not initialized.
  */
  public function getValue(): mixed {
    return $this->value ?? throw new \UnexpectedValueException("Trying to access an uninitialized value");
  }

  /**
  * Sets the value of the element.
  * @param TValue $value The new value of the element.
  */
  public function setValue(mixed $value): void {
    $this->validateValue($value);
    $this->value = $value;
  }
  
}