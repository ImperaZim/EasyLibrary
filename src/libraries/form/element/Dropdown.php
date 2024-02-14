<?php

declare(strict_types=1);

namespace libraries\form\element;

/** @phpstan-extends BaseSelector<int> */
class Dropdown extends BaseSelector {
	protected function getType(): string {
		return "dropdown";
	}

	protected function serializeElementData(): array {
		return [
			"options" => $this->options,
			"default" => $this->default,
		];
	}
}
