<?php

declare(strict_types = 1);

namespace internal\libform\elements;

/**
* Class Dropdown
* @param internal\libform\elements
*/
class Dropdown extends Selector {

  /**
  * Gets the type of the element.
  * @return string The type of the element.
  */
  protected function getType(): string {
    return "dropdown";
  }

  /**
  * Serializes specific element data to an array.
  * @return array<string, mixed> The serialized specific element data.
  */
  protected function serializeElementData(): array {
    return [
      "options" => $this->options,
      "default" => $this->default,
      ];
    }
  }