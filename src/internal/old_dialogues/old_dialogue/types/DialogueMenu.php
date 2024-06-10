<?php

declare(strict_types = 1);

namespace internal\dialogue\types;

use pocketmine\player\Player;
use pocketmine\entity\Entity;
use internal\dialogue\Dialogue;

/**
* Class DialogueMenu
* @package internal\dialogue\types
*/
class DialogueMenu extends Dialogue {
  
  /** #var Entity|null */ 
  private ?Entity $entityTarget;

  /**
  * DialogueMenu constructor.
  * @param string $npcName
  * @param string|null $dialogueBody
  * @param string|null $sceneName
  * @param array $buttons
  */
  public function __construct(
    ?string $npcName = '',
    ?string $dialogueBody = '',
    ?string $sceneName = '',
    ?array $buttons = [],
    ?Entity $entityTarget = null
  ) {
    parent::__construct($npcName);
    $this->entityTarget = $entityTarget;
    $this->setDialogueBody($dialogueBody);
    $this->setSceneName($sceneName);
    if (!empty($buttons)) {
      $this->addButtons($buttons);
    }
  }
  
  /**
   * Gets the target entity 
   * @return Entity|null
   */
  public function getTarget(): ?Entity {
    return $this->entityTarget;
  }

}