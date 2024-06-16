<?php

declare(strict_types = 1);

namespace library\interface;

use pocketmine\player\Player;
use library\interface\exception\InterfaceException;

/**
* Class BaseInterface
* @package library\interface
*/
abstract class BaseInterface {

  /** @var mixed|null */
  private $interface = null;

  /**
  * BaseInterface constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    try {
      $this->interface = $this->structure();
      $this->send();
    } catch (InterfaceException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Send the interface to the player.
  */
  public function send(): void {
    try {
      $interface = $this->interface;
      if ($this->isValidInterface($interface)) {
        $interface->sendTo($this->player);
      }
    } catch (InterfaceException $e) {
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
  * Construct and set up the interface.
  * @return mixed
  */
  protected abstract function structure(): mixed;

  /**
  * Validate the interface type.
  * @param mixed $interface
  * @return bool
  */
  protected abstract function isValidInterface(mixed $interface): bool;

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
    } catch (InterfaceException $e) {
      new \crashdump($e);
    }
    return null;
  }

}
