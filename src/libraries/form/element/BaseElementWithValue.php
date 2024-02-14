<?php

declare(strict_types=1);

namespace libraries\form\element;

/** @phpstan-template TValue */
abstract class BaseElementWithValue extends BaseElement {
	/** @phpstan-param ?TValue $value */
	public function __construct(string $text, protected mixed $value = null) {
		parent::__construct($text);
	}

	/** @phpstan-return TValue */
	public function getValue(): mixed {
		return $this->value ?? throw new \UnexpectedValueException("Trying to access an uninitialized value");
	}

	/** @phpstan-param TValue $value */
	public function setValue(mixed $value): void {
		$this->validateValue($value);
		$this->value = $value;
	}
}
