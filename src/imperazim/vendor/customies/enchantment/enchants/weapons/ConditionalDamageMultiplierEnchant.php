<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants\weapons;

use pocketmine\item\Item;
use pocketmine\event\Event;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\Rarity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use imperazim\vendor\customies\enchantment\PiggyCustomEnchants;
use imperazim\vendor\customies\enchantment\enchants\ReactiveEnchantment;

class ConditionalDamageMultiplierEnchant extends ReactiveEnchantment {

  /**
  * @param callable $condition
  */
  public function __construct(int $id, string $name, private $condition, int $rarity = Rarity::RARE) {
    $this->name = $name;
    $this->rarity = $rarity;
    parent::__construct($id);
  }

  public function getDefaultExtraData(): array {
    return ["additionalMultiplier" => 0.1];
  }

  public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void {
    if ($event instanceof EntityDamageByEntityEvent) {
      if (($this->condition)($event)) {
        $event->setModifier($event->getFinalDamage() * $this->extraData["additionalMultiplier"] * $level, $this->getId());
      }
    }
  }
}