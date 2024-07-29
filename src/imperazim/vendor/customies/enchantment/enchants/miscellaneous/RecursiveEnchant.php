<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants\miscellaneous;

use pocketmine\item\Item;
use pocketmine\event\Event;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use imperazim\vendor\customies\enchantment\enchants\ReactiveEnchantment;

class RecursiveEnchant extends ReactiveEnchantment {

  /** @var bool[] */
  public static array $isUsing;

  public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void {
    if (isset(self::$isUsing[$player->getName()])) return;
    self::$isUsing[$player->getName()] = true;
    $this->safeReact($player, $item, $inventory, $slot, $event, $level, $stack);
    unset(self::$isUsing[$player->getName()]);
  }

  public function safeReact(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void
  {}
}