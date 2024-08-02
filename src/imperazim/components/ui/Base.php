<?php

declare(strict_types = 1);

namespace imperazim\components\ui;

use pocketmine\player\Player;
use imperazim\components\ui\exception\UiException;

/**
* Class Base
* @package imperazim\components\ui
*/
abstract class Base {

  /** @var mixed|null */
  private $ui = null;

  /**
  * Base constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    try {
      $this->ui = $this->structure();
      $this->send();
    } catch (UiException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Send the ui to the player.
  */
  public function send(): void {
    try {
      $ui = $this->ui;
      if ($this->isValid($ui)) {
        $ui->sendTo($this->player);
      }
    } catch (UiException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Get the associated player.
  * @return Player
  */
  public function getPlayer(): Player {
    return $this->player;
  }

  /**
  * Construct and set up the ui.
  * @return mixed
  */
  protected abstract function structure(): mixed;

  /**
  * Validate the ui type.
  * @param mixed $ui
  * @return bool
  */
  protected abstract function isValid(mixed $ui): bool;

  /**
  * Get processed data by key.
  * @param string|null $key
  * @return mixed|null
  */
  public function getProcessedData(?string $key = 'null'): mixed {
    try {
      if ($key != 'null') {
        $value = isset($this->data[$key]) ? $this->data[$key] : null;
      } else {
        $value = $this->data;
      }
      return $value;
    } catch (UiException $e) {
      new \crashdump($e);
    }
    return null;
  }

}
