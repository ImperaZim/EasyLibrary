<?php

declare(strict_types = 1);

namespace internal\dialogue;

use Closure;
use BadMethodCallException;
use pocketmine\player\Player;

/**
* Class Dialogue
* @package internal\dialogue
*/
abstract class Dialogue {

  /**
  * Dialogue constructor.
  * @param string|null $name
  */
  public function __construct(
    private ?string $name = "Default name",
  ) {}

  /**
  * Gets the name of the dialogue.
  * @return string
  */
  public function getName() : string {
    return $this->name;
  }

  /**
  * Sets the name of the dialogue.
  * @param string $name The new name of the dialogue.
  * @return self
  */
  public function setName(string $name) : self {
    $this->name = $name;
    return $this;
  }

  /**
  * Send the dialogue to a player.
  * @param Player $player
  * @param bool $update_existing
  */
  public function sendTo(Player $player, bool $update_existing = false) : void {
    $hooker = new DialogueHooker();
    $hooker->manager !== null || throw new BadMethodCallException("Dialog is not registered");
    $hooker->manager->getPlayer($player)->sendDialogue($this, $update_existing);
  }
}