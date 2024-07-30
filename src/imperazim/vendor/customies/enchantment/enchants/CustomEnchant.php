<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants;

use ReflectionClass;
use pocketmine\player\Player;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Enchantment;

class CustomEnchant extends Enchantment {
  public string $name = "";
  public int $rarity = Rarity::RARE;
  public int $maxLevel = 5;
  private string $displayName;
  public string $description;
  public array $extraData;
  public int $cooldownDuration;
  public int $chance;

  public int $usageType = CustomEnchant::TYPE_HAND;
  public int $itemType = CustomEnchant::ITEM_TYPE_WEAPON;

  /** @var int[] */
  public array $cooldown;

  const TYPE_HAND = 0;
  const TYPE_ANY_INVENTORY = 1;
  const TYPE_INVENTORY = 2;
  const TYPE_ARMOR_INVENTORY = 3;
  const TYPE_HELMET = 4;
  const TYPE_CHESTPLATE = 5;
  const TYPE_LEGGINGS = 6;
  const TYPE_BOOTS = 7;

  const ITEM_TYPE_GLOBAL = 0;
  const ITEM_TYPE_DAMAGEABLE = 1;
  const ITEM_TYPE_WEAPON = 2;
  const ITEM_TYPE_SWORD = 3;
  const ITEM_TYPE_BOW = 4;
  const ITEM_TYPE_TOOLS = 5;
  const ITEM_TYPE_PICKAXE = 6;
  const ITEM_TYPE_AXE = 7;
  const ITEM_TYPE_SHOVEL = 8;
  const ITEM_TYPE_HOE = 9;
  const ITEM_TYPE_ARMOR = 10;
  const ITEM_TYPE_HELMET = 11;
  const ITEM_TYPE_CHESTPLATE = 12;
  const ITEM_TYPE_LEGGINGS = 13;
  const ITEM_TYPE_BOOTS = 14;
  const ITEM_TYPE_COMPASS = 15;

  public function __construct(public int $id) {
    $this->chance = 100;
    $this->cooldownDuration = 0;
    $this->extraData = $this->getDefaultExtraData();
    foreach ($this->getDefaultExtraData() as $key => $value) {
      if (!isset($this->extraData[$key])) {
        $this->extraData[$key] = $value;
      }
    }
    parent::__construct($this->name, $this->rarity, ItemFlags::ALL, ItemFlags::ALL, $this->maxLevel);
  }

  public function getId(): int {
    return $this->id;
  }

  public function getDisplayName(): string {
    return $this->displayName;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function getExtraData(): array {
    return $this->extraData;
  }

  public function getDefaultExtraData(): array {
    return [];
  }

  public function getUsageType(): int {
    return $this->usageType;
  }

  public function getItemType(): int {
    return $this->itemType;
  }

  public function getPriority(): int {
    return 1;
  }

  public function canReact(): bool {
    return false;
  }

  public function canTick(): bool {
    return false;
  }

  public function canToggle(): bool {
    return false;
  }

  public function getCooldown(Player $player): int {
    return ($this->cooldown[$player->getName()] ?? time()) - time();
  }

  public function setCooldown(Player $player, int $cooldown): void {
    $this->cooldown[$player->getName()] = time() + $cooldown;
  }
}