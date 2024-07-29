<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants\traits;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;

trait TickingTrait {

  public function canTick(): bool {
    return true;
  }

  public function getTickingInterval(): int {
    return 1;
  }

  public function onTick(Player $player, Item $item, Inventory $inventory, int $slot, int $level): void {
    if ($this->getCooldown($player) > 0) return;
    $this->tick($player, $item, $inventory, $slot, $level);
  }

  public function tick(Player $player, Item $item, Inventory $inventory, int $slot, int $level): void
  {}

  public function supportsMultipleItems(): bool {
    return false;
  }
}