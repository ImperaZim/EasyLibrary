<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\world\sound\ClickSound;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\utils\CloningRegistryTrait;
use pocketmine\world\sound\Sound as Sounds;
use pocketmine\world\sound\AnvilBreakSound;
use pocketmine\world\sound\EndermanTeleportSound;

/**
* Class Sound
* @package library\utils
*/
final class Sound {
  use CloningRegistryTrait;

  /**
  * Set up and register sounds.
  */
  public static function setup(): void {
    self::register('FAIL', new AnvilBreakSound());
    self::register('CLICK', new ClickSound());
    self::register('SUCCESS', new XpCollectSound());
    self::register('TELEPORT', new EndermanTeleportSound());
  }

  /**
  * Get all registered sounds.
  * @return array
  */
  public static function getAll(): array {
    return self::_registryGetAll();
  }

  /**
  * Register a sound.
  * @param string $name
  * @param Sounds $sound
  */
  protected static function register(string $name, Sounds $sound): void {
    self::_registryRegister($name, $sound);
  }
}