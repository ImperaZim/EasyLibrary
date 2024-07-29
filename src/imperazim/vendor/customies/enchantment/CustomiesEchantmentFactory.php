<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment;

use ReflectionProperty;
use pocketmine\entity\Living;
use pocketmine\utils\StringToTParser;
use pocketmine\item\enchantment\Rarity;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;

/**
* Class CustomiesEnchantmentFactory
* @package imperazim\vendor\customies\enchantment
*/
final class CustomiesEnchantmentFactory {

  /** @var CustomEnchant[] */
  private static array $enchants = [];

  /**
  * Registers a custom enchantment.
  * @param CustomEnchant $enchant
  */
  public static function registerEnchantment(CustomEnchant $enchant): void {
    EnchantmentIdMap::getInstance()->register($enchant->getId(), $enchant);
    self::$enchants[$enchant->getId()] = $enchant;
    self::registerEnchantmentName($enchant);
  }

  /**
  * Unregisters a custom enchantment.
  * @param int|CustomEnchant $id
  */
  public static function unregisterEnchantment(int|CustomEnchant $id): void {
    $id = $id instanceof CustomEnchant ? $id->getId() : $id;
    if (!isset(self::$enchants[$id])) {
      return;
    }

    $enchant = self::$enchants[$id];
    self::unregisterEnchantmentName($enchant);
    self::unregisterEnchantmentId($id);

    unset(self::$enchants[$id]);
  }

  /**
  * Returns all registered custom enchantments.
  * @return CustomEnchant[]
  */
  public static function getEnchantments(): array {
    return self::$enchants;
  }

  /**
  * Returns a custom enchantment by its ID.
  * @param int $id
  * @return CustomEnchant|null
  */
  public static function getEnchantment(int $id): ?CustomEnchant {
    return self::$enchants[$id] ?? null;
  }

  /**
  * Returns a custom enchantment by its name.
  * @param string $name
  * @return CustomEnchant|null
  */
  public static function getEnchantmentByName(string $name): ?CustomEnchant {
    return StringToEnchantmentParser::getInstance()->parse($name) instanceof CustomEnchant
    ? StringToEnchantmentParser::getInstance()->parse($name)
    : null;
  }

  /**
  * Registers the name of a custom enchantment in the StringToEnchantmentParser.
  * @param CustomEnchant $enchant
  */
  private static function registerEnchantmentName(CustomEnchant $enchant): void {
    $parser = StringToEnchantmentParser::getInstance();
    $parser->register($enchant->name, fn() => $enchant);
    if ($enchant->name !== $enchant->getDisplayName()) {
      $parser->register($enchant->getDisplayName(), fn() => $enchant);
    }
  }

  /**
  * Unregisters the name of a custom enchantment in the StringToEnchantmentParser.
  * @param CustomEnchant $enchant
  */
  private static function unregisterEnchantmentName(CustomEnchant $enchant): void {
    $parser = StringToEnchantmentParser::getInstance();
    $callbackMap = self::getPrivateProperty($parser, 'callbackMap');

    unset($callbackMap[strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($enchant->name)))]);
    if ($enchant->name !== $enchant->getDisplayName()) {
      unset($callbackMap[strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($enchant->getDisplayName())))]);
    }

    self::setPrivateProperty($parser, 'callbackMap', $callbackMap);
  }

  /**
  * Unregisters a custom enchantment ID in the EnchantmentIdMap.
  * @param int $id
  */
  private static function unregisterEnchantmentId(int $id): void {
    $idMap = EnchantmentIdMap::getInstance();
    
    $enumToId = self::getPrivateProperty($idMap, 'enumToId');
    unset($enumToId[spl_object_id($idMap->fromId($id))]);
    self::setPrivateProperty($idMap, 'enumToId', $enumToId);
    
    $idToEnum = self::getPrivateProperty($idMap, 'idToEnum');
    unset($idToEnum[$id]);
    self::setPrivateProperty($idMap, 'idToEnum', $idToEnum);
  }

  /**
  * Retrieves a private property value using reflection.
  * @param object $object
  * @param string $property
  * @return mixed
  */
  private static function getPrivateProperty(object $object, string $property): mixed {
    $reflection = new ReflectionProperty($object, $property);
    $reflection->setAccessible(true);
    return $reflection->getValue($object);
  }

  /**
  * Sets a private property value using reflection.
  * @param object $object
  * @param string $property
  * @param mixed $value
  */
  private static function setPrivateProperty(object $object, string $property, mixed $value): void {
    $reflection = new ReflectionProperty($object, $property);
    $reflection->setAccessible(true);
    $reflection->setValue($object, $value);
  }
}