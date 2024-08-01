<?php

declare(strict_types=1);

namespace imperazim\vendor\dialogue\types;

use RuntimeException;
use pocketmine\player\Player;
use imperazim\vendor\dialogue\Dialogue;
use imperazim\vendor\dialogue\textures\DialogueTexture;

final class NullDialogue extends Dialogue {

	public static function instance() : self{
		static $instance = null;
		return $instance ??= new self();
	}

	private function __construct(){
	}

	public function getName() : string{
		throw new RuntimeException("Cannot get name of null dialogue");
	}

	public function getText() : string{
		throw new RuntimeException("Cannot get text of null dialogue");
	}

	public function getTexture() : DialogueTexture{
		throw new RuntimeException("Cannot get texture of null dialogue");
	}

	public function getButtons() : array{
		throw new RuntimeException("Cannot get buttons of null dialogue");
	}

	public function onPlayerRespond(Player $player, int $button) : void{
		throw new RuntimeException("Cannot handle response in a null dialogue");
	}

	public function onPlayerRespondInvalid(Player $player, int $invalid_response) : void{
		throw new RuntimeException("Cannot handle response in a null dialogue");
	}

	public function onPlayerClose(Player $player) : void{
		throw new RuntimeException("Cannot close a null dialogue");
	}

	public function onPlayerDisconnect(Player $player) : void{
		throw new RuntimeException("Cannot handle disconnect in a null dialogue");
	}
}