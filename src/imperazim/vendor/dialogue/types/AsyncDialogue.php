<?php

declare(strict_types=1);

namespace imperazim\vendor\dialogue\dialogue;

use Closure;
use pocketmine\player\Player;
use imperazim\vendor\dialogue\DialogueException;
use imperazim\vendor\dialogue\dialogue\texture\DialogueTexture;

/**
 * @template TResponseType
 */
final class AsyncDialogue extends Dialogue{

	/**
	 * @param string $name
	 * @param string $text
	 * @param DialogueTexture $texture
	 * @param list<DialogueButton> $buttons
	 * @param list<TResponseType> $button_mapping
	 * @param Closure(TResponseType) : void $resolve
	 * @param Closure(DialogueException) : void $reject
	 */
	public function __construct(
		readonly public string $name,
		readonly public string $text,
		readonly public DialogueTexture $texture,
		readonly public array $buttons,
		readonly public array $button_mapping,
		readonly public Closure $resolve,
		readonly public Closure $reject
	){
	  parent::__construct($name);
	}

	public function getName() : string{
		return $this->name;
	}

	public function getText() : string{
		return $this->text;
	}

	public function getTexture() : DialogueTexture{
		return $this->texture;
	}

	public function getButtons() : array{
		return $this->buttons;
	}

	public function onPlayerRespond(Player $player, int $button) : void{
		$this->buttons[$button]->onClick($player);
		($this->resolve)($this->button_mapping[$button]);
	}

	public function onPlayerRespondInvalid(Player $player, int $invalid_response) : void{
		($this->reject)(new DialogueException("Player sent an invalid response ({$invalid_response})", DialogueException::ERR_PLAYER_RESPONSE_INVALID));
	}

	public function onPlayerClose(Player $player) : void{
		($this->reject)(new DialogueException("Player closed", DialogueException::ERR_PLAYER_CLOSED));
	}

	public function onPlayerDisconnect(Player $player) : void{
		($this->reject)(new DialogueException("Player disconnected", DialogueException::ERR_PLAYER_DISCONNECTED));
	}
}