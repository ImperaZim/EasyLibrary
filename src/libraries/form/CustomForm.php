<?php

declare(strict_types=1);

namespace libraries\form;

use Closure;
use pocketmine\utils\Utils;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use libraries\form\element\Input;
use libraries\form\element\Label;
use libraries\form\element\Slider;
use libraries\form\element\Toggle;
use libraries\form\element\Dropdown;
use libraries\form\element\StepSlider;
use libraries\form\element\BaseElement;
use libraries\form\element\BaseElementWithValue;

use function count;
use function gettype;
use function is_array;
use function is_null;

class CustomForm extends BaseForm {
	/**
	 * @phpstan-param (Closure(Player, CustomFormResponse): mixed)|null $onSubmit
	 * @phpstan-param (Closure(Player): mixed)|null $onClose
	 */
	public function __construct(
		string           $title,
		protected array  $elements = [],
		private ?Closure $onSubmit = null,
		private ?Closure $onClose = null,
	) {
		if ($onSubmit !== null) {
			Utils::validateCallableSignature(function(Player $player, CustomFormResponse $response) { }, $onSubmit);
		}
		if ($onClose !== null) {
			Utils::validateCallableSignature(function(Player $player) { }, $onClose);
		}
		parent::__construct($title);
	}

	/**
	 * @phpstan-param Closure(Player, CustomFormResponse): mixed $closure
	 */
	public function onSubmit(Closure $closure): void {
		$this->onSubmit = $closure;
	}

	/**
	 * @phpstan-param Closure(Player): mixed $closure
	 */
	public function onClose(Closure $closure): void {
		$this->onClose = $closure;
	}

	/**
	 * @param string[] $options
	 */
	public function addDropdown(string $text, array $options, int $default = 0): void {
		$this->appendElements(new Dropdown($text, $options, $default));
	}

	/** @phpstan-param BaseElement&BaseElementWithValue<mixed> ...$elements */
	public function appendElements(BaseElement ...$elements): void {
		foreach ($elements as $element) {
			$this->elements[] = $element;
		}
	}

	public function addInput(string $text, string $placeholder = '', string $value = ''): void {
		$this->appendElements(new Input($text, $placeholder, $value));
	}

	public function addSlider(string $text, int $min, int $max, int $step = -1, int $default = -1): void {
		$this->appendElements(new Slider($text, $min, $max, $step, $default));
	}

	/**
	 * @param string[] $steps
	 */
	public function addStepSlider(string $text, array $steps, int $index = -1): void {
		$this->appendElements(new StepSlider($text, $steps, $index));
	}

	public function addToggle(string $text, bool $default = false): void {
		$this->appendElements(new Toggle($text, $default));
	}

	public function addLabel(string $text): void {
		$this->appendElements(new Label($text));
	}

	final public function handleResponse(Player $player, mixed $data): void {
		match (true) {
			is_null($data) => $this->onClose?->__invoke($player),
			is_array($data) => $this->validateElements($player, $data),
			default => throw new FormValidationException('Expected array or null, got ' . gettype($data)),
		};
	}

	/** @phpstan-param array<int, mixed> $data */
	private function validateElements(Player $player, array $data): void {
		if (($actual = count($data)) !== ($expected = count($this->elements))) {
			throw new FormValidationException('Expected ' . $expected . ' result data, got ' . $actual);
		}

		foreach ($data as $index => $value) {
			$element = $this->elements[$index] ?? throw new FormValidationException('Element at offset $index does not exist');
			try {
				$element->setValue($value);
			} catch(FormValidationException $e) {
				throw new FormValidationException('Validation failed for element ' . $element::class . ': ' . $e->getMessage(), 0, $e);
			}
		}

		$this->onSubmit?->__invoke($player, new CustomFormResponse($this->elements));
	}

	protected function getType(): string {
		return 'custom_form';
	}

	protected function serializeFormData(): array {
		return [
			'content' => $this->elements,
		];
	}
}
