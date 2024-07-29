<?php

declare(strict_types = 1);

namespace imperazim\ui\dialogue;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\components\ui\Dialogue;
use imperazim\vendor\dialogue\types\SimpleDialogue;
use imperazim\vendor\dialogue\Dialogue as IDialogue;
use imperazim\vendor\dialogue\elements\DialogueButton;
use imperazim\vendor\dialogue\interaction\DialogueButtonResponse;

/**
* Class SimpleDialogueExample
* @package imperazim\ui\dialogue
*/
final class SimpleDialogueExample extends Dialogue {

  /**
  * Generates and sends the menu to the player.
  */
  public function structure(): IDialogue {
    return new SimpleDialogue(
      name: $this->getName(),
      text: $this->getText(),
      texture: $this->getTexture(),
      buttons: $this->getButtons(),
      onClose: fn($player) => $this->getCloseCallback($player)
    );
  }

  /**
  * Retrieves the name for the dialogue.
  * @return string
  */
  private function getName(): string {
    $file = UiManager::getFile('dialogues');
    return $file->get('simple_dialogue.name', '');
  }

  /**
  * Retrieves the text for the dialogue.
  * @return string
  */
  private function getText(): string {
    $file = UiManager::getFile('dialogues');
    return $file->get('simple_dialogue.text', '');
  }

  /**
  * Retrieves the texture for the scene.
  * @return array
  */
  private function getTexture(): array {
    $file = UiManager::getFile('dialogues');
    return $file->get('simple_dialogue.texture', []);
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return SimpleDialogueButton[]
  */
  private function getButtons(): array {
    $file = UiManager::getFile('dialogues');
    $buttons = [];
    foreach ($file->get('simple_dialogue.buttons', []) as $name) {
      $buttons[] = new DialogueButton(
        name: $name,
        text: '',
        data: null,
        mode: 0,
        type: 1,
        onclick: new DialogueButtonResponse(
          function (Player $player, DialogueButton $button, Dialogue &$dialogue): void {
            $player->sendMessage("you clicked {$button->getName()}");
          }
        )
      );
    }
    return $buttons;
  }

  /**
  * Handles the form closure and returns the next form to display.
  * @param Player $player
  * @return Form|null
  */
  private function getCloseCallback(Player $player): void {
    $player->sendMessage("You closed the dialogue");
  }

}