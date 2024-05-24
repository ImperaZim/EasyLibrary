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

  /**
  * DialogueMenu constructor.
  * @param string $npcName
  * @param string|null $dialogueBody
  * @param string|null $sceneName
  * @param array $buttons
  */
  public function __construct(
    private string $npcName,
    private ?string $dialogueBody = '',
    private ?string $sceneName = '',
    private array $buttons = [],
    private ?Entity $entityTarget
  ) {
    parent::__construct();
    $this->setNpcName($npcName);
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