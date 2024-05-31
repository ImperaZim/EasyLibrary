<?php

declare(strict_types = 1);

namespace internal\libform\interaction;

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
* Class CustomElementsResponse
* @package internal\libform\interaction
*/
final class CustomElementsResponse {
  
  /**
  * CustomElementsResponse constructor.
  * @param Element[] $elements
  */
  public function __construct(private array $elements) {}

  public function getDropdown(): Dropdown {
    return $this->getElement(Dropdown::class);
  }

  public function getInput(): Input {
    return $this->getElement(Input::class);
  }

  public function getSlider(): Slider {
    return $this->getElement(Slider::class);
  }

  public function getStepSlider(): StepSlider {
    return $this->getElement(StepSlider::class);
  }

  public function getToggle(): Toggle {
    return $this->getElement(Toggle::class);
  }

  /**
  * Get all values from elements in the response, excluding labels.
  * @return array
  */
  public function getValues(): array {
    return array_map(fn(Element $element) => $element->getValue(), $this->getNonLabelElements());
  }

  /**
  * Get result from elements by identifier, excluding labels.
  * @param int|null $index
  * @return mixed
  */
  public function getElementResult(?int $index = null): mixed {
    $values = array_map(fn(Element $element) => $element, $this->getNonLabelElements());
    if ($index == null) {
      return $values;
    }
    return $values[$index] ?? null;
  }

  /**
  * Get the first element of the specified type from the response.
  * @param string $expected
  * @return Element
  * @throws UnexpectedValueException
  */
  private function getElement(string $expected): Element {
    foreach ($this->elements as $element) {
      if ($element instanceof Label) {
        continue;
      }
      if ($element instanceof $expected) {
        return $element;
      }
    }
    throw new UnexpectedValueException('No element of the expected type found');
  }

  /**
  * Get all non-label elements from the response.
  * @return Element[]
  */
  private function getNonLabelElements(): array {
    return array_filter($this->elements, fn(Element $element) => !$element instanceof Label);
  }
}