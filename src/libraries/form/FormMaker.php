<?php

namespace libraries\form;

use pocketmine\player\Player;

/**
* Class FormMaker
* @package form
*/
abstract class FormMaker {

  /**
  * @var BaseForm|null
  */
  private ?BaseForm $form = null;

  /**
  * FormMaker constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    $this->makeForm();
  }

  /**
  * Send the form to the player.
  */
  public function send(): void {
    try {
      $form = $this->form;
      if ($form instanceof BaseForm) {
        $form->sendTo($this->player);
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Get the associated player.
  * @return Player
  */
  public function getPlayer() : Player {
    return $this->player;
  }

  /**
  * Construct and set up the form.
  */
  protected abstract function makeForm(): void;

  /**
  * Set the base form.
  * @param BaseForm $form
  * @return $this
  */
  public function setBaseForm(BaseForm $form): self {
    $this->form = $form;
    return $this;
  }

  /**
  * Get the current form instance.
  * @return $this
  */
  protected function getCurrentForm(): self {
    return $this;
  }

  /**
  * Get processed data by key.
  * @param string|null $key
  * @return mixed|null
  */
  public function getProcessedData(?string $key = 'null') : mixed {
    try {
      if ($key != 'null') {
        $value = isset($this->data[$key]) ? $this->data[$key] : null;
      } else {
        $value = $this->data;
      }
      return $value;
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

}