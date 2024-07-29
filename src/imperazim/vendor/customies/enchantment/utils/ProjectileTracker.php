<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\utils;

use pocketmine\item\Item;
use pocketmine\entity\projectile\Projectile;
use pocketmine\item\enchantment\EnchantmentInstance;

class ProjectileTracker {
  
  /** @var Item[] */
  public static array $projectile = [];

  public static function addProjectile(Projectile $projectile, Item $item): void {
    self::$projectile[$projectile->getId()] = $item;
  }

  public static function isTrackedProjectile(Projectile $projectile): bool {
    return isset(self::$projectile[$projectile->getId()]);
  }

  public static function getItem(Projectile $projectile): ?Item {
    if (!isset(self::$projectile[$projectile->getId()])) return null;
    return self::$projectile[$projectile->getId()];
  }

  /**
  * @return EnchantmentInstance[]
  */
  public static function getEnchantments(Projectile $projectile): array {
    if (!isset(self::$projectile[$projectile->getId()])) return [];
    $item = self::$projectile[$projectile->getId()];
    return $item->getEnchantments();
  }

  public static function removeProjectile(Projectile $projectile): void {
    if (!isset(self::$projectile[$projectile->getId()])) return;
    unset(self::$projectile[$projectile->getId()]);
  }
}