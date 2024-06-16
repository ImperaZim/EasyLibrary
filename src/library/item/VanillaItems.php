<?php

declare(strict_types = 1);

namespace library\item;

use Exception;
use library\item\exception\ItemException;

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
  * @throws ItemException
  */
  public static function setup() : void {
    try {
      foreach (ItemFactory::getRegisteredItems() as $name => $item) {
        self::register($name, $item);
      }
    } catch (Exception $e) {
      throw new ItemException("Failed to set up registered items: " . $e->getMessage());
    }
  }

  /**
  * Get all registered items.
  * @return Item[]
  * @throws ItemException
  */
  public static function getAll() : array {
    try {
      return self::_registryGetAll();
    } catch (Exception $e) {
      throw new ItemException("Failed to get all registered items: " . $e->getMessage());
    }
  }

  /**
  * Register an item.
  * @param string $name
  * @param Item $item
  * @throws ItemException
  */
  protected static function register(string $name, Item $item) : void {
    try {
      self::_registryRegister($name, $item);
    } catch (Exception $e) {
      throw new ItemException("Failed to register item '$name': " . $e->getMessage());
    }
  }

}
