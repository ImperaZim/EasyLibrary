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

  private ?PlayerManager $manager = null;

  /**
  * DialogueHooker constructor.
  * @param PluginBase|null $registrant
  */
  public function __construct(private ?PluginBase $registrant = null) {
    if ($registrant != null) {
      $this->register();
    }
  }

  public function isRegistered() : bool {
    return $this->manager !== null;
  }

  public function register(?PluginBase $registrant) : void {
    $this->manager === null || throw new BadMethodCallException("Dialogue is already registered");
    $this->manager = new PlayerManager();
    $this->manager->init($registrant);
  }

}