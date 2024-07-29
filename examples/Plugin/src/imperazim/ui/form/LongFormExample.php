<?php

declare(strict_types = 1);

namespace imperazim\ui\form;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\components\ui\Form;
use imperazim\vendor\libform\Form as IForm;
use imperazim\vendor\libform\types\LongForm;
use imperazim\vendor\libform\elements\Title;
use imperazim\vendor\libform\elements\Image;
use imperazim\vendor\libform\elements\Button;
use imperazim\vendor\libform\elements\Content;
use imperazim\vendor\libform\interaction\ButtonResponse;

/**
* Class FarmForm
* @package imperazim\ui\form
*/
final class FarmForm extends Form {

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
    $file = UiManager::getFile('forms');
    return new Title(
      text: $file->get('long_form.title', '')
    );
  }

  /**
  * Gets the content.
  * @return Content
  */
  private function getContent(): Content {
    $file = UiManager::getFile('forms');
    return new Content(
      text: $file->get('long_form.content', '')
    );
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return Button[]
  */
  private function getButtons(): array {
    $player = $this->getPlayer();
    $file = UiManager::getFile('forms');
    $buttonList = $file->get('long_form.buttons', []);

    $buttons = [];
    foreach ($buttonList as $button => $data) {
      $buttons[] = new Button(
        text: $data['text'],
        image: Image::fromString($data['icon']),
        value: $button,
        onclick: new ButtonResponse(
          function (Player $player, Button $button): void {
            $player->sendMessage('You are clicked ' . $button->getValue());
          }
        ),
        reopen: $data['reopen']
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
    $player->sendMessage('Long Form Closed');
  }
}