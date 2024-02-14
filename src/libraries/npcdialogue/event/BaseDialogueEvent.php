<?php

declare(strict_types = 1);

namespace libraries\npcdialogue\event;

use pocketmine\event\Event;
use libraries\npcdialogue\NpcDialogue;

/**
* Class BaseDialogueEvent
* @package libraries\npcdialogue\event
*/
abstract class BaseDialogueEvent extends Event {

  /**
  * @var NpcDialogue
  */
  protected NpcDialogue $dialogue;

  /**
  * BaseDialogueEvent constructor.
  * @param NpcDialogue $dialogue
  */
  public function __construct(NpcDialogue $dialogue) {
    $this->dialogue = $dialogue;
  }

  /**
  * @return NpcDialogue
  */
  public function getDialogue() : NpcDialogue {
    return $this->dialogue;
  }
}