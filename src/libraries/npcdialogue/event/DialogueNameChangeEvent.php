<?php

namespace libraries\npcdialogue\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

use libraries\npcdialogue\NpcDialogue;

/**
* Class DialogueNameChangeEvent
* @package libraries\npcdialogue\event
*/
final class DialogueNameChangeEvent extends BaseDialogueEvent implements Cancellable {
  use CancellableTrait;

  /**
  * @var string
  */
  protected string $oldName;

  /**
  * @var string
  */
  protected string $newName;

  /**
  * DialogueNameChangeEvent constructor.
  * @param NpcDialogue $dialogue
  * @param string $oldName
  * @param string $newName
  */
  public function __construct(NpcDialogue $dialogue, string $oldName, string $newName) {
    parent::__construct($dialogue);
    $this->oldName = $oldName;
    $this->newName = $newName;
  }

  /**
  * @return string
  */
  public function getOldName() : string {
    return $this->oldName;
  }

  /**
  * @return string
  */
  public function getNewName() : string {
    return $this->newName;
  }

  /**
  * @param string $newName
  */
  public function setNewName(string $newName) : void {
    $this->newName = $newName;
  }
}