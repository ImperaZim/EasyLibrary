<?php

declare(strict_types = 1);

namespace ImperaZim\forms;

use pocketmine\player\Player;

use library\interface\Form;
use ImperaZim\PluginExample;
use internal\libform\Form as IForm;
use internal\libform\elements\Title;
use internal\libform\types\LongForm;
use internal\libform\elements\Image;
use internal\libform\elements\Button;
use internal\libform\elements\Content;
use internal\libform\interaction\ButtonResponse;

/**
* Class ExampleLongForm
* @package ImperaZim\forms
*/
class ExampleLongForm extends Form {

  /**
  * Defines the form structure.
  */
  public function structure(): IForm {
    return new LongForm(
      title: $this->getTitle(),
      content: $this->getContent(),
      buttons: $this->getButtons(),
      onClose: fn($player) => $this->getCloseCallback($player)
    );
  }
  
  /**
  * Gets the title.
  * @return Title
  */
  private function getTitle(): Title {
    return new Title(
      text: PluginExample::getSettings('long_form_title', '')
    );
  }

  /**
  * Gets the content.
  * @return Content
  */
  private function getContent(): Content {
    return new Content(
      text: PluginExample::getSettings('long_form_content', '')
    );
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return Button[]
  */
  private function getButtons(): array {
    $buttons = [];
    foreach (PluginExample::getSettings('long_form_buttons', []) as $button_value => $data) {
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
    $player->sendMessage('LongForm closed!');
  }
}