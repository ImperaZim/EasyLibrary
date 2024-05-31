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

  /**
  * Get all values from elements in the response, excluding labels.
  * @return array
  */
  public function getValues(): array {
    return array_map(fn(Element $element) => $element->getValue(), $this->getNonLabelElements());
  }

  /**
  * Get specific element by your identifier
  * @param string $id
  * @return Element|null
  */
  public function getElement(string $id): ?Element {
    return array_filter($this->elements, fn(Element $element) => $element->getIdentifier() === $id);
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