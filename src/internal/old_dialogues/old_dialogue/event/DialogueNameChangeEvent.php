<?php

declare(strict_types = 1);

namespace internal\dialogue\event;

use internal\dialogue\Dialogue;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

/**
* Class DialogueNameChangeEvent
* @package internal\dialogue\event
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
  * @param Dialogue $dialogue
  * @param string $oldName
  * @param string $newName
  */
  public function __construct(Dialogue $dialogue, string $oldName, string $newName) {
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