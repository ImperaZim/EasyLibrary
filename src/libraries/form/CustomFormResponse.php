<?php

declare(strict_types = 1);

namespace libraries\form;

use UnexpectedValueException;

use libraries\form\element\Input;
use libraries\form\element\Label;
use libraries\form\element\Slider;
use libraries\form\element\Toggle;
use libraries\form\element\Dropdown;
use libraries\form\element\StepSlider;
use libraries\form\element\BaseElement;
use libraries\form\element\BaseElementWithValue;

use function is_null;
use function array_shift;

class CustomFormResponse {
  /** @phpstan-param list<BaseElement&BaseElementWithValue<mixed>> $elements */
  public function __construct(
    private array $elements
  ) {}

  public function getDropdown(): Dropdown {
    return $this->get(Dropdown::class);
  }

  /**
  * @template T&BaseElement&BaseElementWithValue<mixed>
  * @phpstan-param class-string<T&BaseElement&BaseElementWithValue<mixed>> $expected
  * @phpstan-return T&BaseElement&BaseElementWithValue<mixed>
  */
  public function get(string $expected): BaseElement {
    $element = array_shift($this->elements);
    return match (true) {
      is_null($element) => throw new UnexpectedValueException('There are no elements in the container'),
      $element instanceof Label => $this->get($expected),
      //skip labels
      !($element instanceof $expected) => throw new UnexpectedValueException('Unexpected type of element'),
      default => $element,
      };
    }

    public function getInput(): Input {
      return $this->get(Input::class);
    }

    public function getSlider(): Slider {
      return $this->get(Slider::class);
    }

    public function getStepSlider(): StepSlider {
      return $this->get(StepSlider::class);
    }

    public function getToggle(): Toggle {
      return $this->get(Toggle::class);
    }

    /** @phpstan-return list<mixed> */
    public function getValues(): array {
      $values = [];

      foreach ($this->elements as $element) {
        if ($element instanceof Label) {
          continue;
        }

          //$element instanceof Dropdown => $element->getSelectedOption(),
        $values[] = match (true) {
        default => $element->getValue(),
        };
      }

      return $values;
    }
  }