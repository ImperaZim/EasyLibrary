<?php

declare(strict_types = 1);

namespace internal\libform\types;

use Closure;
use internal\libform\Form;
use pocketmine\utils\Utils;
use pocketmine\player\Player;
use internal\libform\elements\Title;
use internal\libform\elements\Content;
use internal\libform\elements\ModalButton;
use pocketmine\form\FormValidationException;
/**
* Class ModalForm
* @package internal\libform\types
*/
final class ModalForm extends Form {

  /**
  * ModalForm constructor.
  * @param Title $title
  * @param Content $content
  * @param ModalButton $buttonYes
  * @param ModalButton $buttonNo
  */
  public function __construct(
    public ?Title $title = new Title(''),
    protected ?Content $content = new Content(''),
    public ModalButton $buttonYes = new ModalButton('gui.yes'),
    public ModalButton $buttonNo = new ModalButton('gui.no')
  ) {
    parent::__construct($title);
  }

  /**
  * Get the content of the form.
  * @return Content|null
  */
  public function getContent(): ?Content {
    return $this->content;
  }

  /**
  * Set the content of the form.
  * @param Content|null $content
  * @return self
  */
  public function setContent(?Content $content = new Content('')): self {
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
  public function setButtonYes(?ModalButton $buttonYes = new ModalButton('gui.yes')): self {
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
  public function setButtonNo(?ModalButton $buttonNo = new ModalButton('gui.no')): self {
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
  * @return 
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
      'content' => $this->getContent()->getText(),
      'button1' => $this->getButtonYes()->getText(),
      'button2' => $this->getButtonNo()->getText(),
    ];
  }

}