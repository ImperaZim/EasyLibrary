<?php

declare(strict_types = 1);

namespace internal\libform\handler;

use UnexpectedValueException;

use internal\libform\elements\Input;
use internal\libform\elements\Label;
use internal\libform\elements\Slider;
use internal\libform\elements\Toggle;
use internal\libform\elements\Element;
use internal\libform\elements\Dropdown;
use internal\libform\elements\StepSlider;
use internal\libform\elements\ElementWithValue;

/**
* Class CustomFormResponse
* @package internal\libform\handler
*/
final class CustomFormResponse {

  /**
  * CustomFormResponse constructor.
  * @param Element[] $elements
  * @phpstan-param list<Element&ElementWithValue<mixed>> $elements
  */
  public function __construct(private array $elements) {}

  /**
  * Get the first Dropdown element from the response.
  * @return Dropdown
  * @throws UnexpectedValueException
  */
  public function getDropdown(): Dropdown {
    return $this->get(Dropdown::class);
  }

  /**
  * Get the first Input element from the response.
  * @return Input
  * @throws UnexpectedValueException
  */
  public function getInput(): Input {
    return $this->get(Input::class);
  }

  /**
  * Get the first Slider element from the response.
  * @return Slider
  * @throws UnexpectedValueException
  */
  public function getSlider(): Slider {
    return $this->get(Slider::class);
  }

  /**
  * Get the first StepSlider element from the response.
  * @return StepSlider
  * @throws UnexpectedValueException
  */
  public function getStepSlider(): StepSlider {
    return $this->get(StepSlider::class);
  }

  /**
  * Get the first Toggle element from the response.
  * @return Toggle
  * @throws UnexpectedValueException
  */
  public function getToggle(): Toggle {
    return $this->get(Toggle::class);
  }

  /**
  * Get all values from elements in the response, excluding labels.
  * @return array
  * @phpstan-return list<mixed>
  */
  public function getValues(): array {
    $values = [];
    foreach ($this->elements as $element) {
      if ($element instanceof Label) {
        continue;
      }
      $values[] = $element->getValue();
    }
    return $values;
  }

  /**
  * Get the first element of the specified type from the response.
  * @template T of Element&ElementWithValue<mixed>
  * @param string $expected
  * @phpstan-param class-string<T> $expected
  * @return T
  * @throws UnexpectedValueException
  */
  private function get(string $expected): Element {
    $element = array_shift($this->elements);
    while ($element instanceof Label) {
      $element = array_shift($this->elements);
    }
    if (is_null($element)) {
      throw new UnexpectedValueException('There are no elements in the container');
    }
    if (!($element instanceof $expected)) {
      throw new UnexpectedValueException('Unexpected type of element');
    }
    return $element;
  }
}