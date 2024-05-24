<?php

namespace library\interface;

use pocketmine\player\Player;
use internal\libform\Form as IForm;

/**
* Class Form
* @package library\interface
*/
abstract class Form {

  /** @var IForm|null */
  private ?IForm $form = null;

  /**
  * Form constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    $this->form = $this->structure();
    $this->send();
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
  protected abstract function structure(): IForm;

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