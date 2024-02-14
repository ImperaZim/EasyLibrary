<?php

declare(strict_types=1);

namespace libraries\form\element;

use JetBrains\PhpStorm\Immutable;
use libraries\form\traits\IdentifiableElement;

abstract class BaseElement implements \JsonSerializable {
	use IdentifiableElement;

	public function __construct(
		#[Immutable] public /*readonly*/ string $text
	) {
	}

	/** @phpstan-return array<string, mixed> */
	final public function jsonSerialize(): array {
		$ret = $this->serializeElementData();
		$ret["type"] = $this->getType();
		$ret["text"] = $this->text;

		return $ret;
	}

	/** @phpstan-return array<string, mixed> */
	abstract protected function serializeElementData(): array;

	abstract protected function getType(): string;

	abstract protected function validateValue(mixed $value): void;
}
