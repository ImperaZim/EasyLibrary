<?php

declare(strict_types = 1);

namespace internal\dialogue\event;

use pocketmine\event\Event;
use internal\dialogue\Dialogue;

/**
* Class BaseDialogueEvent
* @package internal\dialogue\event
*/
abstract class BaseDialogueEvent extends Event {

  /** @var Dialogue */
  protected Dialogue $dialogue;

  /**
  * BaseDialogueEvent constructor.
  * @param Dialogue $dialogue
  */
  public function __construct(Dialogue $dialogue) {
    $this->dialogue = $dialogue;
  }

  /**
  * @return Dialogue
  */
  public function getDialogue() : Dialogue {
    return $this->dialogue;
  }
}