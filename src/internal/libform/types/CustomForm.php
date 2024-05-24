<?php

declare(strict_types = 1);

namespace internal\libform\types;

use Closure;
use pocketmine\utils\Utils;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use internal\libform\Form;
use internal\libform\elements\Input;
use internal\libform\elements\Label;
use internal\libform\elements\Slider;
use internal\libform\elements\Toggle;
use internal\libform\elements\Element;
use internal\libform\elements\Dropdown;
use internal\libform\elements\StepSlider;
use internal\libform\elements\ElementWithValue;
use internal\libform\handler\CustomFormResponse;

/**
* Class CustomForm
* @package internal\libform\types
*/
final class CustomForm extends Form {

  /**
  * CustomForm constructor.
  * @param string $title
  * @param Element[] $elements
  * @param (Closure(Player, CustomFormResponse): mixed)|null $onSubmit
  * @param (Closure(Player): mixed)|null $onClose
  */
  public function __construct(
    string $title,
    protected array $elements = [],
    private ?Closure $onSubmit = null,
    private ?Closure $onClose = null,
  ) {
    if ($onSubmit !== null) {
      Utils::validateCallableSignature(function(Player $player, CustomFormResponse $response) {}, $onSubmit);
    }
    if ($onClose !== null) {
      Utils::validateCallableSignature(function(Player $player) {}, $onClose);
    }
    parent::__construct($title);
  }

  /**
  * Set the callback to be executed when the form is submitted.
  * @param Closure(Player, CustomFormResponse): void $closure
  */
  public function onSubmit(Closure $closure): void {
    $this->onSubmit = $closure;
  }

  /**
  * Set the callback to be executed when the form is closed.
  * @param Closure(Player): void $closure
  */
  public function onClose(Closure $closure): void {
    $this->onClose = $closure;
  }

  /**
  * Add a dropdown element to the form.
  * @param string $text
  * @param string[] $options
  * @param int $default
  */
  public function addDropdown(string $text, array $options, int $default = 0): void {
    $this->appendElements(new Dropdown($text, $options, $default));
  }

  /**
  * Append multiple elements to the form.
  * @param Element ...$elements @phpstan-param Element&ElementWithValue<mixed> ...$elements
  */
  public function appendElements(Element ...$elements): void {
    foreach ($elements as $element) {
      $this->elements[] = $element;
    }
  }

  /**
  * Add an input element to the form.
  * @param string $text
  * @param string $placeholder
  * @param string $value
  */
  public function addInput(string $text, string $placeholder = '', string $value = ''): void {
    $this->appendElements(new Input($text, $placeholder, $value));
  }

  /**
  * Add a slider element to the form.
  * @param string $text
  * @param int $min
  * @param int $max
  * @param int $step
  * @param int $default
  */
  public function addSlider(string $text, int $min, int $max, int $step = -1, int $default = -1): void {
    $this->appendElements(new Slider($text, $min, $max, $step, $default));
  }

  /**
  * Add a step slider element to the form.
  * @param string $text
  * @param string[] $steps
  * @param int $index
  */
  public function addStepSlider(string $text, array $steps, int $index = -1): void {
    $this->appendElements(new StepSlider($text, $steps, $index));
  }

  /**
  * Add a toggle element to the form.
  * @param string $text
  * @param bool $default
  */
  public function addToggle(string $text, bool $default = false): void {
    $this->appendElements(new Toggle($text, $default));
  }

  /**
  * Add a label element to the form.
  *
  * @param string $text
  */
  public function addLabel(string $text): void {
    $this->appendElements(new Label($text));
  }

  /**
  * Handle the response from the form.
  * @param Player $player
  * @param mixed $data
  * @throws FormValidationException
  */
  final public function handleResponse(Player $player, mixed $data): void {
    if (is_null($data)) {
      if ($this->onClose !== null) {
        ($this->onClose)($player);
      }
    } elseif (is_array($data)) {
      $this->validateElements($player, $data);
    } else {
      throw new FormValidationException('Expected array or null, got ' . gettype($data));
    }
  }

  /**
  * Validate the elements in the form.
  * @param Player $player
  * @param array<int, mixed> $data
  * @throws FormValidationException
  */
  private function validateElements(Player $player, array $data): void {
    if (($actual = count($data)) !== ($expected = count($this->elements))) {
      throw new FormValidationException('Expected ' . $expected . ' result data, got ' . $actual);
    }

    foreach ($data as $index => $value) {
      $element = $this->elements[$index] ?? throw new FormValidationException("Element at offset $index does not exist");
      try {
        $element->setValue($value);
      } catch (FormValidationException $e) {
        throw new FormValidationException('Validation failed for element ' . $element::class . ': ' . $e->getMessage(), 0, $e);
      }
    }

    if ($this->onSubmit !== null) {
      ($this->onSubmit)($player, new CustomFormResponse($this->elements));
    }
  }

  /**
  * Get the type of the form.
  * @return string
  */
  protected function getType(): string {
    return 'custom_form';
  }

  /**
  * Serialize the form data for transmission.
  * @return array
  */
  protected function serializeFormData(): array {
    return [
      'content' => $this->elements,
    ];
  }
  
}