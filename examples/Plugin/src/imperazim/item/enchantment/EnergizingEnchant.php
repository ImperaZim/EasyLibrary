<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants\tools;

use pocketmine\item\Item;
use pocketmine\event\Event;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;
use imperazim\vendor\customies\enchantment\enchants\ReactiveEnchantment;

class EnergizingEnchant extends ReactiveEnchantment {

  protected string $name = 'Energizing';
  protected string $itemType = CustomEnchant::ITEM_TYPE_TOOLS;

  /**
  * EnergizingEnchant constructor.
  */
  public function __construct() {
    parent::__construct(EnchantmentIds::ENERGIZING);
  }

  /**
  * @return string[]
  */
  public function getReagent(): array {
    return [BlockBreakEvent::class];
  }

  /**
  * @return array<string, int>
  */
  public function getDefaultExtraData(): array {
    return [
      'duration' => 20,
      'baseAmplifier' => -1,
      'amplifierMultiplier' => 1,
    ];
  }

  /**
  * React to the enchantment trigger.
  * @param Player $player
  * @param Item $item
  * @param Inventory $inventory
  * @param int $slot
  * @param Event $event
  * @param int $level
  * @param int $stack
  * @return void
  */
  public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void {
    if ($event instanceof BlockBreakEvent) {
      if (!$player->getEffects()->has(VanillaEffects::HASTE())) {
        $effect = new EffectInstance(
          VanillaEffects::HASTE(),
          $this->extraData['duration'],
          $level * $this->extraData['amplifierMultiplier'] + $this->extraData['baseAmplifier'],
          false
        );
        $player->getEffects()->add($effect);
      }
    }
  }
}