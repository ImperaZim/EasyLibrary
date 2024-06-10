<?php

declare(strict_types=1);

namespace internal\dialogue\dialogue;

use internal\dialogue\dialogue\texture\DialogueTexture;
use pocketmine\player\Player;

interface Dialogue{

	public function getName() : string;

	public function getText() : string;

	public function getTexture() : DialogueTexture;

	/**
	 * @return DialogueButton[]
	 */
	public function getButtons() : array;

	public function onPlayerRespond(Player $player, int $button) : void;

	public function onPlayerRespondInvalid(Player $player, int $invalid_response) : void;

	public function onPlayerClose(Player $player) : void;

	public function onPlayerDisconnect(Player $player) : void;
}