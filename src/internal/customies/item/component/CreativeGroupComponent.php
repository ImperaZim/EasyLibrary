<?php
declare(strict_types=1);

namespace internal\customies\item\component;

use internal\customies\item\CreativeInventoryInfo;

final class CreativeGroupComponent implements ItemComponent {

	private CreativeInventoryInfo $creativeInfo;

	public function __construct(CreativeInventoryInfo $creativeInfo) {
		$this->creativeInfo = $creativeInfo;
	}

	public function getName(): string {
		return "creative_group";
	}

	public function getValue(): string {
		return $this->creativeInfo->getGroup();
	}

	public function isProperty(): bool {
		return true;
	}
}