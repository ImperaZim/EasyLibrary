<?php

declare(strict_types = 1);

namespace ImperaZim\dialogues;

use pocketmine\player\Player;

use ImperaZim\PluginExample;
use library\interface\Dialogue;
use internal\dialogue\types\SimpleDialogue;
use internal\dialogue\Dialogue as IDialogue;
use internal\dialogue\elements\DialogueButton;
use internal\dialogue\interaction\DialogueButtonResponse;

/**
* Class ExampleDialogue
* @package ImperaZim\dialogues
*/
final class ExampleDialogue extends Dialogue {

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
    return PluginExample::getSettings('dialogue_name', 'Example Dialogue Name');
  }

  /**
  * Retrieves the text for the dialogue.
  * @return string
  */
  private function getText(): string {
    return PluginExample::getSettings('dialogue_text', 'Example Dialogue Text');
  }

  /**
  * Retrieves the texture for the scene.
  * @return array
  */
  private function getTexture(): array {
    return [
      'type' => PluginExample::getSettings('dialogue_texture.type', 'dialogue:default'),
      'data' => PluginExample::getSettings('dialogue_texture.typeId', 0)
    ];
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return SimpleDialogueButton[]
  */
  private function getButtons(): array {
    $buttons = [];
    foreach (PluginExample::getSettings('dialogue_buttons', []) as $index => $name) {
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