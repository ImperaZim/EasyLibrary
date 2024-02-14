<?php

declare(strict_types=1);

namespace libraries\form;

use Closure;
use pocketmine\utils\Utils;
use pocketmine\player\Player;
use pocketmine\form\FormValidationException;

use function gettype;
use function is_bool;

class ModalForm extends BaseForm {
	/** @phpstan-param Closure(Player, bool): mixed $onSubmit */
	public function __construct(
		string $title,
		protected string $content = '',
		private ?Closure $onSubmit = null,
		public string $button1 = 'gui.yes',
		public string $button2 = 'gui.no',
	) {
		parent::__construct($title);
	}

	public function onSubmit(Closure $closure): void {
		$this->onSubmit = $closure;
	}

	public function getContent(): string {
		return $this->content;
	}

	public function setContent(string $content): void {
		$this->content = $content;
	}

	public function setButton1(string $button1): void {
		$this->button1 = $button1;
	}

	public function setButton2(string $button2): void {
		$this->button2 = $button2;
	}

	/** @phpstan-param Closure(Player): mixed $onConfirm */
	public static function confirm(string $title, string $content, Closure $onConfirm): self {
		Utils::validateCallableSignature(function(Player $player) { }, $onConfirm);
		return new self($title, $content, static function(Player $player, bool $response) use ($onConfirm): void {
			if ($response) {
				$onConfirm($player);
			}
		});
	}

	final public function handleResponse(Player $player, mixed $data): void {
		if (!is_bool($data)) {
			throw new FormValidationException('Expected bool, got ' . gettype($data));
		}

		$this->onSubmit?->__invoke($player, $data);
	}

	protected function getType(): string {
		return 'modal';
	}

	protected function serializeFormData(): array {
		return [
			'content' => $this->content,
			'button1' => $this->button1,
			'button2' => $this->button2,
		];
	}
}
