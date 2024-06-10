<?php

declare(strict_types=1);

namespace internal\dialogue\dialogue;

use Closure;
use pocketmine\player\Player;

final class DialogueButton implements DialogueButton{

	/**
	 * @param string $name
	 * @param (Closure(Player, self) : void)|null $onClick
	 * @return self
	 */
	public static function simple(string $name, ?Closure $onClick = null) : self{
		return new self($name, "", null, 0, 1, $onClick);
	}

	/**
	 * @param string $name
	 * @param string $text
	 * @param string|null $data
	 * @param int $mode
	 * @param int $type
	 * @param (Closure(Player) : void)|null $onClick
	 */
	private function __construct(
		private string $name,
		private string $text,
		private ?string $data,
		private int $mode,
		private int $type,
		private ?Closure $onClick
	){}

	public function getName() : string{
		return $this->name;
	}

	public function getText() : string{
		return $this->text;
	}

	public function getData() : ?string{
		return $this->data;
	}

	public function getMode() : int{
		return $this->mode;
	}

	public function getType() : int{
		return $this->type;
	}

	public function onClick(Player $player) : void{
		if($this->on_click !== null){
			($this->on_click)($player, $this);
		}
	}
}