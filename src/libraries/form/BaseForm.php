<?php

declare(strict_types=1);

namespace libraries\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

abstract class BaseForm implements Form {
	public function __construct(public string $title) {
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function setTitle(string $title): void {
		$this->title = $title;
	}

	public function sendTo(Player $player): void {
		$player->sendForm($this);
	}

	/** @phpstan-return array<string, mixed> */
	final public function jsonSerialize(): array {
		$data = $this->serializeFormData();
		$data['type'] = $this->getType();
		$data['title'] = $this->title;
		return $data;
	}

	/** @phpstan-return array<string, mixed> */
	abstract protected function serializeFormData(): array;

	abstract protected function getType(): string;
}
