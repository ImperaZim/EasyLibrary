<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants\armor;

use pocketmine\item\Item;
use pocketmine\event\Event;
use pocketmine\player\Player;
use pocketmine\entity\Living;
use pocketmine\inventory\Inventory;
use pocketmine\entity\effect\Effect;
use pocketmine\item\enchantment\Rarity;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;
use imperazim\vendor\customies\enchantment\enchants\ReactiveEnchantment;

class AttackerDeterrentEnchant extends ReactiveEnchantment {

  public int $usageType = CustomEnchant::TYPE_ARMOR_INVENTORY;
  public int $itemType = CustomEnchant::ITEM_TYPE_ARMOR;

  /**
  * @param Effect[] $effects
  * @param int[] $durationMultiplier
  * @param int[] $amplifierMultiplier
  */
  public function __construct(int $id, string $name, private array $effects, private array $durationMultiplier, private array $amplifierMultiplier, int $rarity = Rarity::RARE) {
    $this->name = $name;
    $this->rarity = $rarity;
    parent::__construct($id);
  }

  public function getDefaultExtraData(): array {
    return ["durationMultipliers" => $this->durationMultiplier,
      "amplifierMultipliers" => $this->amplifierMultiplier];
  }

  public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void {
    if ($event instanceof EntityDamageByEntityEvent) {
      $damager = $event->getDamager();
      if ($damager instanceof Living) {
        foreach ($this->effects as $key => $effect) {
          $damager->getEffects()->add(new EffectInstance($effect, $this->extraData["durationMultipliers"][$key] * $level, $this->extraData["amplifierMultipliers"][$key] * $level));
        }
      }
    }
  }
}