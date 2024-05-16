<?php

namespace forms;

use form\FormBase;
use libraries\form\ModalForm;
use pocketmine\player\Player;

/**
* Class ExampleModalForm
* @package forms
*/
class ExampleModalForm extends FormBase {

  /**
  * Generates and sends the form to the player.
  */
  public function makeForm() : void {
    try {
      $this->setFormBase(
        form: new ModalForm(
          title: 'Example Modal Form',
          content: 'Any Content :)',
          button1: 'agree',
          button2: 'cancel',
          onSubmit: fn($player, $value) => $this->getSubmitCallback(
            player: $player,
            value: $value
          )
        )
      )->send();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Handles the form submission and returns the next form to display.
  * @param Player $player
  * @param bool $value
  * @return FormBase|null
  */
  private function getSubmitCallback(Player $player, bool $value): ?FormBase {
    try {
      if ($value) {
        $player->sendMessage('Agree Clicked!');
      } else {
        $player->sendMessage('Cancel Clicked!');
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

}