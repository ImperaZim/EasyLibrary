<?php

declare(strict_types = 1);

namespace internal\libform\types;

use Closure;
use internal\libform\Form;
use pocketmine\utils\Utils;
use pocketmine\player\Player;
use internal\libform\elements\ModalButton;
use pocketmine\form\FormValidationException;
/**
* Class ModalForm
* @package internal\libform\types
*/
final class ModalForm extends Form {

  /**
  * ModalForm constructor.
  * @param string $title
  * @param string $content
  * @param ModalButton $buttonYes
  * @param ModalButton $buttonNo
  */
  public function __construct(
    public string $title,
    protected string $content = '',
    public ModalButton $buttonYes = new ModalButton('gui.yes'),
    public ModalButton $buttonNo = new ModalButton('gui.no')
  ) {
    parent::__construct($title);
  }

  /**
  * Get the content of the form.
  * @return string
  */
  public function getContent(): string {
    return $this->content;
  }

  /**
  * Set the content of the form.
  * @param string $content
  * @return self
  */
  public function setContent(string $content): self {
    $this->content = $content;
    return $this;
  }

  /**
  * Get the button Yes.
  * @return ModalButton
  */
  public function getButtonYes(): ModalButton {
    return $this->buttonYes;
  }

  /**
  * Set the button Yes.
  * @param ModalButton $buttonYes
  * @return self
  */
  public function setButtonYes(ModalButton $buttonYes): self {
    $this->buttonYes = $buttonYes;
    return $this;
  }

  /**
  * Get the button No.
  * @return ModalButton
  */
  public function getButtonNo(): ModalButton {
    return $this->buttonNo;
  }
  
  /**
  * Set the button No.
  * @param ModalButton $buttself
  * @return self
  */
  public function setButtonNo(ModalButton $buttonNo): self {
    $this->buttonNo = $buttonNo;
    return $this;
  }

  /**
  * Handle the response from the form.
  * @param Player $player
  * @param mixed $data
  * @throws FormValidationException
  */
  final public function handleResponse(Player $player,
    mixed $data): void {
    if (!is_bool($data)) {
      throw new FormValidationException('Expected bool, got ' . gettype($data));
    }
    if ($data) {
      $button = $this->getButtonYes();
      $buttonResponse = $button->getResponse();
      if ($buttonResponse !== null) {
        $buttonResponse->runAt($player);
      }
    } else {
      $button = $this->getButtonNo();
      $buttonResponse = $button->getResponse();
      if ($buttonResponse !== null) {
        $buttonResponse->runAt($player);
      }
    }
  }

  /**
  * Get the type of the form.
  * @return string
  */
  protected function getType(): string {
    return 'modal';
  }

  /**
  * Serialize the form data for transmission.
  * @return array
  */
  protected function serializeFormData(): array {
    return [
      'content' => $this->getContent(),
      'button1' => $this->getButtonYes()->getText(),
      'button2' => $this->getButtonNo()->getText(),
    ];
  }

}