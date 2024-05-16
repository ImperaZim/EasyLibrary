<?php

declare(strict_types = 1);

namespace menu;

use pocketmine\player\Player;
use libraries\invmenu\InvMenu;

/**
* Class InvMenuBase
* @package menu
*/
abstract class InvMenuBase implements InvMenuInterface {
  
  /** @var InvMenu|null */
  private ?InvMenu $menu = null;

  /**
  * InvMenuBase constructor.
  * @param Player $player
  * @param array|null $data
  */
  public function __construct(
    private Player $player,
    private ?array $data = []
  ) {
    $this->makeMenu();
  }

  /**
  * Send the menu to the player.
  */
  public function send(): void {
    try {
      if ($this->menu instanceof InvMenu) {
        $this->menu->send($this->player);
      }
    } catch (\Throwable $e) {
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
  * Construct and set up the menu.
  */
  protected abstract function makeMenu(): void;

  /**
  * Set the base menu.
  * @param InvMenu $menu
  * @return $this
  */
  public function setMenuBase(InvMenu $menu): self {
    $this->menu = $menu;
    return $this;
  }

  /**
  * Get the current menu instance.
  * @return InvMenu|null
  */
  protected function getCurrentMenu(): ?InvMenu {
    return $this->menu;
  }

  /**
  * Get processed data by key.
  * @param string|null $key
  * @return mixed|null
  */
  public function getProcessedData(?string $key = null): mixed {
    try {
      return $key !== null && isset($this->data[$key]) ? $this->data[$key] : $this->data;
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }
}