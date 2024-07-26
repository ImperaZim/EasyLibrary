<?php

declare(strict_types = 1);

namespace imperazim\vendor\libform;

use pocketmine\player\Player;
use pocketmine\form\Form as IForm;
use imperazim\vendor\libform\elements\Title;

/**
* Class Form
* @package imperazim\vendor\libform
*/
abstract class Form implements IForm {

  /**
  * Form constructor.
  * @param Title $title The title of the form.
  */
  public function __construct(
    protected ?Title $title = new Title('')
  ) {}

  /**
  * Gets the title of the form.
  * @return Title The title of the form.
  */
  public function getTitle(): ?Title {
    return $this->title;
  }

  /**
  * Sets the title of the form.
  * @param Title $title The new title of the form.
  */
  public function setTitle(?Title $title = new Title('')): void {
    $this->title = $title;
  }

  /**
  * Sends the form to the specified player.
  * @param Player $player The player to send the form to.
  */
  public function sendTo(Player $player): void {
    $player->sendForm($this);
  }

  /**
  * Serializes the form data to an array.
  * @return array<string, mixed> The serialized form data.
  */
  final public function jsonSerialize(): array {
    $data = $this->serializeFormData();
    $data['type'] = $this->getType();
    $data['title'] = $this->getTitle()->getText();
    return $data;
  }

  /**
  * Serializes specific form data to an array.
  * @return array<string, mixed> The serialized specific form data.
  */
  abstract protected function serializeFormData(): array;

  /**
  * Gets the type of the form.
  * @return string The type of the form.
  */
  abstract protected function getType(): string;

}