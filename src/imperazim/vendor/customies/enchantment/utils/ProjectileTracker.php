<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\utils;

use pocketmine\item\Item;
use pocketmine\entity\projectile\Projectile;
use pocketmine\item\enchantment\EnchantmentInstance;

/**
* Class ProjectileTracker
* @package imperazim\vendor\customies\enchantment\utils
*/
class ProjectileTracker {

  /** @var Item[] */
  private static array $projectiles = [];

  /**
  * Adds a projectile to the tracker.
  * @param Projectile $projectile
  * @param Item $item
  */
  public static function addProjectile(Projectile $projectile, Item $item): void {
    self::$projectiles[$projectile->getId()] = $item;
  }

  /**
  * Checks if a projectile is being tracked.
  * @param Projectile $projectile
  * @return bool
  */
  public static function isTrackedProjectile(Projectile $projectile): bool {
    return isset(self::$projectiles[$projectile->getId()]);
  }

  /**
  * Retrieves the item associated with a projectile.
  * @param Projectile $projectile
  * @return Item|null
  */
  public static function getItem(Projectile $projectile): ?Item {
    return self::$projectiles[$projectile->getId()] ?? null;
  }

  /**
  * Retrieves the enchantments of the item associated with a projectile.
  * @param Projectile $projectile
  * @return EnchantmentInstance[]
  */
  public static function getEnchantments(Projectile $projectile): array {
    $item = self::getItem($projectile);
    return $item ? $item->getEnchantments() : [];
  }

  /**
  * Removes a projectile from the tracker.
  * @param Projectile $projectile
  */
  public static function removeProjectile(Projectile $projectile): void {
    unset(self::$projectiles[$projectile->getId()]);
  }
}