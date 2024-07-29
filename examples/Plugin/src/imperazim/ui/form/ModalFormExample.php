<?php

declare(strict_types = 1);

namespace imperazim\ui\form;

use pocketmine\player\Player;

use imperazim\ui\UiManager;
use imperazim\components\ui\Form;
use imperazim\vendor\libform\Form as IForm;
use imperazim\vendor\libform\elements\Title;
use imperazim\vendor\libform\types\ModalForm;
use imperazim\vendor\libform\elements\Content;
use imperazim\vendor\libform\elements\ModalButton;
use imperazim\vendor\libform\interaction\ModalButtonResponse;

/**
* Class ModalFormExample
* @package imperazim\ui\form
*/
class ModalFormExample extends Form {

  /**
  * Defines the form structure.
  * @return IForm
  */
  public function structure(): IForm {
    return new ModalForm(
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
    $file = UiManager::getFile('forms');
    return new Title(
      text: $file->get('modal_form.title', '')
    );
  }

  /**
  * Gets the content.
  * @return Content
  */
  private function getContent(): Content {
    $file = UiManager::getFile('forms');
    return new Content(
      text: $file->get('modal_form.content', '')
    );
  }

  /**
  * Gets the 'YES' modal button.
  * @return ModalButton
  */
  private function getModalButtonYes(): ModalButton {
    $file = UiManager::getFile('forms');
    return new ModalButton(
      text: $file->get('modal_form.button_yes.text', ''),
      onclick: new ModalButtonResponse(
        function (Player $player): void {
          $player->sendMessage('You are clicked confirm button.');
        }
      )
    );
  }

  /**
  * Gets the 'NO' modal button.
  * @return ModalButton
  */
  private function getModalButtonNo(): ModalButton {
    $file = UiManager::getFile('forms');
    return new ModalButton(
      text: $file->get('modal_form.button_no.text', ''),
      onclick: new ModalButtonResponse(
        function (Player $player): void {
          $player->sendMessage('You are clicked cancel button.');
        }
      )
    );
  }
}