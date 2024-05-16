<?php

namespace forms;

use form\FormBase;
use libraries\form\MenuForm;
use pocketmine\player\Player;
use libraries\form\menu\Image;
use libraries\form\menu\Button;

/**
* Class ExampleMenuForm
* @package forms
*/
class ExampleMenuForm extends FormBase {

  /**
  * Generates and sends the form to the player.
  */
  public function makeForm() : void {
    try {
      $this->setFormBase(
        form: new MenuForm(
          title: "Example Menu Form",
          content: '§7Any text:',
          buttons: (array) $this->getButtons(),
          onSubmit: fn($player, $button) => $this->getSubmitCallback(
            player: $player,
            button: $button
          ),
          onClose: fn($player) => $this->getCloseCallback($player)
        )
      )->send();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return Button[]
  */
  private function getButtons(): array {
    $buttons = [];
    $player = $this->getPlayer();

    $buttons[] = new Button(
      text: 'Button with one line',
      image: Image::null(),
      value: 'button_1'
    );

    $buttons[] = new Button(
      text: ['Button with', 'two lines'],
      image: Image::null(),
      value: 'button_2'
    );

    $buttons[] = new Button(
      text: 'Button with path image',
      image: Image::path('textures/items/ender_pearl.png'),
      value: 'button_3'
    );

    $buttons[] = new Button(
      text: 'Button with url image',
      image: Image::url('https://picsum.photos/200/200'),
      value: 'button_4'
    );

    return $buttons;
  }

  /**
  * Handles the form submission and returns the next form to display.
  * @param Player $player
  * @param Button $button
  * @return FormBase|null
  */
  private function getSubmitCallback(Player $player, Button $button): ?FormBase {
    try {
      $player->sendMessage($button->getValue() . ' clicked!');
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Handles the form closure and returns the next form to display.
  * @param Player $player
  * @return FormBase|null
  */
  private function getCloseCallback(Player $player): ?FormBase {
    $player->sendMessage('Form closed!');
    return null;
  }

}