<?php

declare(strict_types = 1);

namespace library\item;

use pocketmine\item\Item;
use pocketmine\utils\CloningRegistryTrait;

/**
* Class VanillaItems
* @package library\item
*/
final class VanillaItems {
  use CloningRegistryTrait;

  /**
  * Set up registered items.
  */
  public static function setup() : void {
    foreach (ItemFactory::$registeredItems as $name => $item) {
      self::register($name, $item);
    }
  }

  /**
  * Get all registered items.
  * @return Item[]
  */
  public static function getAll() : array {
    return self::_registryGetAll();
  }

  /**
  * Register a title.
  * @param string $name
  * @param Item $item
  */
  protected static function register(string $name, Item $item) : void {
    self::_registryRegister($name, $item);
  }

}