<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform\elements;

use pocketmine\form\FormValidationException;

/**
* Class StepSlider
* @param imperazim\vendor\libform\elements
*/
class StepSlider extends Selector {

  /**
  * Gets the type of the element.
  * @return string The type of the element.
  */
  protected function getType(): string {
    return "step_slider";
  }

  /**
  * Serializes specific element data to an array.
  * @return array<string, mixed> The serialized specific element data.
  */
  protected function serializeElementData(): array {
    return [
      "steps" => $this->options,
      "default" => $this->default
    ];
  }
}