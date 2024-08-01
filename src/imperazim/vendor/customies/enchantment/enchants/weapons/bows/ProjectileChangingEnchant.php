<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\enchants\weapons\bows;

use pocketmine\item\Item;
use pocketmine\event\Event;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\Rarity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use imperazim\vendor\customies\enchantment\utils\Utils;
use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;
use imperazim\vendor\customies\enchantment\utils\ProjectileTracker;
use imperazim\vendor\customies\enchantment\enchants\ReactiveEnchantment;

class ProjectileChangingEnchant extends ReactiveEnchantment { 
  
  public int $itemType = CustomEnchant::ITEM_TYPE_BOW;

  /**
  * @phpstan-param class-string<Entity> $projectileType
  */
  public function __construct(int $id, string $name, private string $projectileType, int $maxLevel = 1, int $rarity = Rarity::RARE) {
    $this->name = $name;
    $this->rarity = $rarity;
    $this->maxLevel = $maxLevel;
    parent::__construct($id);
  }

  public function getReagent(): array {
    return [EntityShootBowEvent::class];
  }

  public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void {
    if ($event instanceof EntityShootBowEvent) {
      /** @var Projectile $projectile */
      $projectile = $event->getProjectile();
      ProjectileTracker::removeProjectile($projectile);

      $newProjectile = Utils::createNewProjectile($this->projectileType, $projectile->getLocation(), $player, $projectile, $level);
      $newProjectile->setMotion($projectile->getMotion());
      $newProjectile->spawnToAll();

      $event->setProjectile($newProjectile);
      ProjectileTracker::addProjectile($newProjectile, $item);
    }
  }

  public function getPriority(): int {
    return 2;
  }
}