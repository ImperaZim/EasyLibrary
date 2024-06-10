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
    $this->manager === null || throw new BadMethodCallException("Dialogue is already registered");
    $this->manager = new PlayerManager();
    $this->manager->init($this->registrant);
  }
}