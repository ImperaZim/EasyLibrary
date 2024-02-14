<?php

declare(strict_types=1);

namespace libraries\form\menu;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class Image implements \JsonSerializable {
	private function __construct(public /*readonly*/ string $data, public /*readonly*/ string $type) {
	}

	public static function url(string $data): self {
		return new self($data, "url");
	}

	public static function site(string $name): self {
		return self::url(
		  'https://imperazim.cloud/skyblock/images/' . $name . '.png'
		);
	}

	public static function path(string $data): self {
		return new self($data, "path");
	}
	
	public static function null(): null {
		return null;
	}

	/** @phpstan-return array<string, mixed> */
	public function jsonSerialize(): array {
		return [
			"type" => $this->type,
			"data" => $this->data,
		];
	}
}
