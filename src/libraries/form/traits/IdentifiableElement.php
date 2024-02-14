<?php

declare(strict_types=1);

namespace libraries\form\traits;

trait IdentifiableElement {
	private mixed $identifier = null;

	public function getIdentifier(): mixed {
		return $this->identifier;
	}

	public function setIdentifier(mixed $identifier): void {
		$this->identifier = $identifier;
	}
}
