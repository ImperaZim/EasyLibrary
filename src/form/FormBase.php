<?php

namespace form;

use pocketmine\player\Player;
use libraries\form\BaseForm as IForm;

/**
* Class FormBase
* @package form
*/
abstract class FormBase implements FormInterface {

  /** @var IForm|null */
  private ?IForm $form = null;

  /**
  * FormBase constructor.
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
      if ($form instanceof IForm) {
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
  * @param IForm $form
  * @return $this
  */
  public function setFormBase(IForm $form): self {
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