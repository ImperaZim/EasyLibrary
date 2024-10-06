<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\interaction;

use UnexpectedValueException;
use imperazim\vendor\libform\elements\Input;
use imperazim\vendor\libform\elements\Label;
use imperazim\vendor\libform\elements\Slider;
use imperazim\vendor\libform\elements\Toggle;
use imperazim\vendor\libform\elements\Element;
use imperazim\vendor\libform\elements\Dropdown;
use imperazim\vendor\libform\elements\StepSlider;
use imperazim\vendor\libform\elements\ElementWithValue;

/**
* Class CustomElementsResponse
* @package imperazim\vendor\libform\interaction
*/
final class CustomElementsResponse {

  /**
  * CustomElementsResponse constructor.
  * @param Element[] $elements
  */
  public function __construct(private array $elements) {}

  /**
  * Get all values from elements in the response, excluding labels.
  * @return array
  */
  public function getValues(): array {
    return array_map(fn(Element $element) => $element->getValue(), $this->getNonLabelElements());
  }

  /**
  * Get all elements
  * @return Element[]
  */
  public function getElements(): array {
    return $this->getNonLabelElements();
  }

  /**
  * Get specific element by your identifier
  * @param string $id
  * @return Element|null
  */
  public function getElement(string $id): ?Element {
    foreach ($this->elements as $element) {
      if ($element->getIdentifier() === $id) {
        return $element;
      }
    }
    return null;
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
  * Get all non-label elements from the response.
  * @return Element[]
  */
  private function getNonLabelElements(): array {
    return array_filter($this->elements, fn(Element $element) => !$element instanceof Label);
  }
}