<?php

declare(strict_types = 1);

namespace ImperaZim\forms;

use pocketmine\player\Player;

use library\interface\Form;
use ImperaZim\PluginExample;
use internal\libform\Form as IForm;
use internal\libform\elements\Title;
use internal\libform\types\ModalForm;
use internal\libform\elements\Content;
use internal\libform\elements\ModalButton;
use internal\libform\interaction\ModalButtonResponse;

/**
* Class ExampleModalForm
* @package ImperaZim\forms
*/
class ExampleModalForm extends Form {

  /**
  * Defines the form structure.
  * @return IForm
  */
  public function structure(): IForm {
    return new LongForm(
      title: $this->getTitle(),
      content: $this->getContent(),
      buttonYes: $this->getModalButtonYes(),
      buttonNo: $this->getModalButtonNo()
    );
  }

  /**
  * Gets the title.
  * @return Title
  */
  private function getTitle(): Title {
    return new Title(
      text: PluginExample::getSettings('modal_form_title', '')
    );
  }

  /**
  * Gets the content.
  * @return Content
  */
  private function getContent(): Content {
    return new Content(
      text: PluginExample::getSettings('modal_form_content', '')
    );
  }

  /**
  * Gets the 'YES' modal button.
  * @return ModalButton
  */
  private function getModalButtonYes(): ModalButton {
    return new ModalButton(
      text: PluginExample::getSettings('modal_form_buttons.button_yes', 'gui.yes'),
      onclick: new ModalButtonResponse(
        function (Player $player): void {
          $player->sendMessage("you confirmed!");
        }
      )
    );
  }

  /**
  * Gets the 'NO' modal button.
  * @return ModalButton
  */
  private function getModalButtonNo(): ModalButton {
    return new ModalButton(
      text: PluginExample::getSettings('modal_form_buttons.button_no', 'gui.no'),
      onclick: new ModalButtonResponse(
        function (Player $player): void {
          $player->sendMessage("you canceled!");
        }
      ) // It is not necessary to add the button click response!
    );
  }
}