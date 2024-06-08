<?php

declare(strict_types = 1);

namespace ImperaZim\dialogues;

use ImperaZim\PluginExample;
use library\interface\Dialogue;
use internal\dialogue\types\DialogueMenu;
use internal\dialogue\form\DialogueButton;
use internal\dialogue\Dialogue as IDialogue;
use internal\dialogue\form\DialogueButtonResponse;

/**
* Class ExampleDialogue
* @package ImperaZim\dialogues
*/
final class ExampleDialogue extends Dialogue {

  /**
  * Generates and sends the menu to the player.
  */
  public function structure(): IDialogue {
    return new DialogueMenu(
      npcName: $this->getNpcName(),
      dialogueBody: $this->getDialogueBody(),
      sceneName: $this->getSceneName(),
      buttons: $this->getButtons(),
      entityTarget: null
    );
  }

  /**
  * Retrieves the name for the menu.
  * @return string
  */
  private function getNpcName(): string {
    return PluginExample::getSettings('dialogue_title', 'Example Dialogue Name');
  }

  /**
  * Retrieves the text for the body.
  * @return string
  */
  private function getDialogueBody(): string {
    return PluginExample::getSettings('dialogue_body', 'Example Dialogue Body');
  }

  /**
  * Retrieves the name for the scene.
  * @return string
  */
  private function getSceneName(): string {
    return PluginExample::getSettings('dialogue_scene_name', 'Example Dialogue Scene Name');
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return Button[]
  */
  private function getButtons(): array {
    $buttons = [];
    foreach (PluginExample::getSettings('dialogue_form_buttons', []) as $index => $data) {
      $buttons[] = new DialogueButton(
        name: $data['name'],
        text: $data['text'],
        buttonResponse: new DialogueButtonResponse(
          function(Player $player, DialogueButton $button) : void {
            $player->sendMessage("you clicked {$button->getName()}");
          }
        )
      );
    }
    return $buttons;
  }

}