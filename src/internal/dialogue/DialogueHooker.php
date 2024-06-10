<?php

declare(strict_types = 1);

namespace internal\dialogue;

use BadMethodCallException;
use pocketmine\plugin\PluginBase;
use internal\dialogue\player\PlayerManager;

/**
* Class DialogueHooker
* @package internal\dialogue
*/
final class DialogueHooker {

  /** @var PlayerManager|null */
  private ?PlayerManager $manager = null;

  /**
  * DialogueHooker constructor.
  * @param PluginBase|null $registrant Plugin registrant.
  */
  public function __construct(private ?PluginBase $registrant = null) {
    if ($registrant != null) {
      $this->register();
    }
  }

  /**
  * Sets the player manager instance.
  * @param PlayerManager|null $manager
  * @return self
  */
  public function setPlayerManager(?PlayerManager $manager): self {
    $this->manager = $manager;
    return $this;
  }

  /**
  * Gets the player manager instance.
  * @return PlayerManager|null
  */
  public function getPlayerManager(): ?PlayerManager {
    return $this->manager;
  }

  /**
  * Checks if the dialogue is registered.
  * @return bool Returns true if the dialogue is registered, false otherwise.
  */
  public function isRegistered() : bool {
    return $this->manager !== null;
  }

  /**
  * Registers the dialogue.
  * @throws BadMethodCallException If the dialogue is already registered.
  */
  public function register() : void {
    if (!$hooker->isRegistered()) {
      throw new BadMethodCallException("Dialogue is already registered");
    }
    $this->setPlayerManager(new PlayerManager());
    $this->getPlayerManager()->init($this->registrant);
  }
}