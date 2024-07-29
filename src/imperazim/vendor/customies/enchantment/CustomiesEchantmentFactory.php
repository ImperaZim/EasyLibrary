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
* Class CustomiesEchantmentFactory
* @package imperazim\vendor\customies\enchantment
*/
final class CustomiesEchantmentFactory {

  /** @var CustomEnchant[] */
  public static array $enchants = [];

  public static function registerEnchantment(CustomEnchant $enchant): void {
    EnchantmentIdMap::getInstance()->register($enchant->getId(), $enchant);
    self::$enchants[$enchant->getId()] = $enchant;
    StringToEnchantmentParser::getInstance()->register($enchant->name, fn() => $enchant);
    if ($enchant->name !== $enchant->getDisplayName()) StringToEnchantmentParser::getInstance()->register($enchant->getDisplayName(), fn() => $enchant);
  }

  public static function unregisterEnchantment(int|CustomEnchant $id): void {
    $id = $id instanceof CustomEnchant ? $id->getId() : $id;
    $enchant = self::$enchants[$id];

    $property = new ReflectionProperty(StringToTParser::class, "callbackMap");
    $property->setAccessible(true);
    $value = $property->getValue(StringToEnchantmentParser::getInstance());
    unset($value[strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($enchant->name)))]);
    if ($enchant->name !== $enchant->getDisplayName()) unset($value[strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($enchant->getDisplayName())))]);
    $property->setValue(StringToEnchantmentParser::getInstance(), $value);
    unset(self::$enchants[$id]);

    $property = new ReflectionProperty(EnchantmentIdMap::class, "enumToId");
    $property->setAccessible(true);
    $value = $property->getValue(EnchantmentIdMap::getInstance());
    unset($value[spl_object_id(EnchantmentIdMap::getInstance()->fromId($id))]);
    $property->setValue(EnchantmentIdMap::getInstance(), $value);

    $property = new ReflectionProperty(EnchantmentIdMap::class, "idToEnum");
    $property->setAccessible(true);
    $value = $property->getValue(EnchantmentIdMap::getInstance());
    unset($value[$id]);
    $property->setValue(EnchantmentIdMap::getInstance(), $value);
  }

  /**
  * @return CustomEnchant[]
  */
  public static function getEnchantments(): array {
    return self::$enchants;
  }

  public static function getEnchantment(int $id): ?CustomEnchant {
    return self::$enchants[$id] ?? null;
  }

  public static function getEnchantmentByName(string $name): ?CustomEnchant {
    return ($enchant = StringToEnchantmentParser::getInstance()->parse($name)) instanceof CustomEnchant ? $enchant : null;
  }
}