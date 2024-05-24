<?php

namespace library\interface;

use pocketmine\player\Player;
use internal\invmenu\InvMenu as IMenu;

/**
* Class Menu
* @package library\interface
*/
abstract class Menu {

  /** @var IMenu|null */
  private ?IMenu $menu = null;

  /**
  * Menu constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    $this->menu = $this->structure();
    $this->send();
  }

  /**
  * Send the menu to the player.
  */
  public function send(): void {
    try {
      $menu = $this->menu;
      if ($menu instanceof IMenu) {
        $menu->send($this->player);
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
  * Construct and set up the menu.
  */
  protected abstract function structure(): void;

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