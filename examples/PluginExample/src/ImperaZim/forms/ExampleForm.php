<?php

namespace ImperaZim\forms;

use pocketmine\player\Player;

use library\interface\Form;
use ImperaZim\PluginExample;
use internal\libform\Form as IForm;
use internal\libform\types\LongForm;
use internal\libform\elements\Image;
use internal\libform\elements\Button;
use internal\libform\interaction\ButtonResponse;

/**
* Class ExampleForm
* @package ImperaZim\forms
*/
class ExampleForm extends Form {

  /**
  * Defines the form structure.
  */
  public function structure(): IForm {
    return new LongForm(
      title: "Example Form",
      content: '§7Any text:',
      buttons: $this->getButtons(),
      onClose: fn($player) => $this->getCloseCallback($player)
    );
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return Button[]
  */
  private function getButtons(): array {
    $buttons = [];
    $buttons[] = new Button(
      text: 'Created by ImperaZim',
      image: Image::null(),
      value: 'ImperaZim',
      onclick: new ButtonResponse(
        function (Player $player, Button $button): void {
          $player->sendMessage("you clicked {$button->getValue()}");
        }
      ),
      reopen: false
    );
    foreach (PluginExample::getSettings('form_buttons', []) as $button_value => $data) {
      $buttons[] = new Button(
        text: $data['text'],
        image: Image::fromString($data['image']),
        value: $button_value,
        onclick: new ButtonResponse(
          function (Player $player, Button $button): void {
            $player->sendMessage("you clicked {$button->getValue()}");
          }
        ),
        reopen: false
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
    $player->sendMessage('Form closed!');
  }
}