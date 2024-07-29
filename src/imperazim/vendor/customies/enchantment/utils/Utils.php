<?php

declare(strict_types = 1);

namespace imperazim\vendor\customies\enchantment\utils;

use imperazim\vendor\customies\enchantment\enchants\CustomEnchant;
use imperazim\vendor\customies\enchantment\CustomiesEchantmentManager;

use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\Armor;
use pocketmine\item\Shears;
use pocketmine\item\Shovel;
use pocketmine\item\Pickaxe;
use pocketmine\item\Compass;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;

class Utils {
  const TYPE_NAMES = [
    CustomEnchant::ITEM_TYPE_ARMOR => "Armor",
    CustomEnchant::ITEM_TYPE_HELMET => "Helmet",
    CustomEnchant::ITEM_TYPE_CHESTPLATE => "Chestplate",
    CustomEnchant::ITEM_TYPE_LEGGINGS => "Leggings",
    CustomEnchant::ITEM_TYPE_BOOTS => "Boots",
    CustomEnchant::ITEM_TYPE_WEAPON => "Weapon",
    CustomEnchant::ITEM_TYPE_SWORD => "Sword",
    CustomEnchant::ITEM_TYPE_BOW => "Bow",
    CustomEnchant::ITEM_TYPE_TOOLS => "Tools",
    CustomEnchant::ITEM_TYPE_PICKAXE => "Pickaxe",
    CustomEnchant::ITEM_TYPE_AXE => "Axe",
    CustomEnchant::ITEM_TYPE_SHOVEL => "Shovel",
    CustomEnchant::ITEM_TYPE_HOE => "Hoe",
    CustomEnchant::ITEM_TYPE_DAMAGEABLE => "Damageable",
    CustomEnchant::ITEM_TYPE_GLOBAL => "Global",
    CustomEnchant::ITEM_TYPE_COMPASS => "Compass",
  ];

  const RARITY_NAMES = [
    Rarity::COMMON => "Common",
    Rarity::UNCOMMON => "Uncommon",
    Rarity::RARE => "Rare",
    Rarity::MYTHIC => "Mythic"
  ];

  /** @var int[] */
  public static array $shouldTakeFallDamage;

  public static function getRomanNumeral(int $integer): string {
    $romanNumeralConversionTable = [
      'M' => 1000,
      'CM' => 900,
      'D' => 500,
      'CD' => 400,
      'C' => 100,
      'XC' => 90,
      'L' => 50,
      'XL' => 40,
      'X' => 10,
      'IX' => 9,
      'V' => 5,
      'IV' => 4,
      'I' => 1
    ];
    $romanString = "";
    while ($integer > 0) {
      foreach ($romanNumeralConversionTable as $rom => $arb) {
        if ($integer >= $arb) {
          $integer -= $arb;
          $romanString .= $rom;
          break;
        }
      }
    }
    return $romanString;
  }

  public static function isHelmet(Item $item): bool {
    return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_HEAD;
  }

  public static function isChestplate(Item $item): bool {
    return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_CHEST;
  }

  public static function isLeggings(Item $item): bool {
    return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_LEGS;
  }

  public static function isBoots(Item $item): bool {
    return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_FEET;
  }

  public static function itemMatchesItemType(Item $item, int $itemType): bool {
    return match ($itemType) {
      CustomEnchant::ITEM_TYPE_GLOBAL => true,
      CustomEnchant::ITEM_TYPE_DAMAGEABLE => $item instanceof Durable,
      CustomEnchant::ITEM_TYPE_WEAPON => $item instanceof Sword || $item instanceof Axe || $item instanceof Bow,
      CustomEnchant::ITEM_TYPE_SWORD => $item instanceof Sword,
      CustomEnchant::ITEM_TYPE_BOW => $item instanceof Bow,
      CustomEnchant::ITEM_TYPE_TOOLS => $item instanceof Pickaxe || $item instanceof Axe || $item instanceof Shovel || $item instanceof Hoe || $item instanceof Shears,
      CustomEnchant::ITEM_TYPE_PICKAXE => $item instanceof Pickaxe,
      CustomEnchant::ITEM_TYPE_AXE => $item instanceof Axe,
      CustomEnchant::ITEM_TYPE_SHOVEL => $item instanceof Shovel,
      CustomEnchant::ITEM_TYPE_HOE => $item instanceof Hoe,
      CustomEnchant::ITEM_TYPE_ARMOR => $item instanceof Armor,
      CustomEnchant::ITEM_TYPE_HELMET => self::isHelmet($item),
      CustomEnchant::ITEM_TYPE_CHESTPLATE => self::isChestplate($item),
      CustomEnchant::ITEM_TYPE_LEGGINGS => self::isLeggings($item),
      CustomEnchant::ITEM_TYPE_BOOTS => self::isBoots($item),
      CustomEnchant::ITEM_TYPE_COMPASS => $item instanceof Compass,
      default => false,
      };
    }

    public static function checkEnchantIncompatibilities(Item $item, CustomEnchant $enchant): bool {
      foreach ($item->getEnchantments() as $enchantment) {
        $otherEnchant = $enchantment->getType();
        if (!$otherEnchant instanceof CustomEnchant) continue;
      }
      return true;
    }

    public static function displayEnchants(ItemStack $itemStack): ItemStack {
      $item = TypeConverter::getInstance()->netItemStackToCore($itemStack);
      if (count($item->getEnchantments()) > 0) {
        $additionalInformation = TextFormat::RESET . TextFormat::WHITE . $item->getName();
        foreach ($item->getEnchantments() as $enchantmentInstance) {
          $enchantment = $enchantmentInstance->getType();
          if ($enchantment instanceof CustomEnchant) {
            $additionalInformation .= "\n" . TextFormat::RESET . Utils::getColorFromRarity($enchantment->getRarity()) . $enchantment->getDisplayName() . " " . Utils::getRomanNumeral($enchantmentInstance->getLevel());
          }
        }
        if ($item->getNamedTag()->getTag(Item::TAG_DISPLAY)) $item->getNamedTag()->setTag("OriginalDisplayTag", $item->getNamedTag()->getTag(Item::TAG_DISPLAY)->safeClone());
        $item = $item->setCustomName($additionalInformation);
      }
      return TypeConverter::getInstance()->coreItemStackToNet($item);
    }

    public static function filterDisplayedEnchants(ItemStack $itemStack): ItemStack {
      $item = TypeConverter::getInstance()->netItemStackToCore($itemStack);
      $tag = $item->getNamedTag();
      if (count($item->getEnchantments()) > 0) $tag->removeTag(Item::TAG_DISPLAY);
      if ($tag->getTag("OriginalDisplayTag") instanceof CompoundTag) {
        $tag->setTag(Item::TAG_DISPLAY, $tag->getTag("OriginalDisplayTag"));
        $tag->removeTag("OriginalDisplayTag");
      }
      $item->setNamedTag($tag);
      return TypeConverter::getInstance()->coreItemStackToNet($item);
    }

    /**
    * @param EnchantmentInstance[] $enchantments
    * @return EnchantmentInstance[]
    */
    public static function sortEnchantmentsByPriority(array $enchantments): array {
      usort($enchantments, function (EnchantmentInstance $enchantmentInstance, EnchantmentInstance $enchantmentInstanceB) {
        $type = $enchantmentInstance->getType();
        $typeB = $enchantmentInstanceB->getType();
        return ($typeB instanceof CustomEnchant ? $typeB->getPriority() : 1) - ($type instanceof CustomEnchant ? $type->getPriority() : 1);
      });
      return $enchantments;
    }

    public static function getColorFromRarity(int $rarity): string {
      return self::getTFConstFromString([
        "common" => "yellow",
        "uncommon" => "blue",
        "rare" => "gold",
        "mythic" => "light_purple"
      ][strtolower(self::RARITY_NAMES[$rarity])]);
    }

    public static function getTFConstFromString(string $color): string {
      $colorConversionTable = [
        "BLACK" => TextFormat::BLACK,
        "DARK_BLUE" => TextFormat::DARK_BLUE,
        "DARK_GREEN" => TextFormat::DARK_GREEN,
        "DARK_AQUA" => TextFormat::DARK_AQUA,
        "DARK_RED" => TextFormat::DARK_RED,
        "DARK_PURPLE" => TextFormat::DARK_PURPLE,
        "GOLD" => TextFormat::GOLD,
        "GRAY" => TextFormat::GRAY,
        "DARK_GRAY" => TextFormat::DARK_GRAY,
        "BLUE" => TextFormat::BLUE,
        "GREEN" => TextFormat::GREEN,
        "AQUA" => TextFormat::AQUA,
        "RED" => TextFormat::RED,
        "LIGHT_PURPLE" => TextFormat::LIGHT_PURPLE,
        "YELLOW" => TextFormat::YELLOW,
        "WHITE" => TextFormat::WHITE
      ];
      return $colorConversionTable[strtoupper($color)] ?? TextFormat::GRAY;
    }

    public static function shouldTakeFallDamage(Player $player): bool {
      return !isset(self::$shouldTakeFallDamage[$player->getName()]);
    }

    public static function setShouldTakeFallDamage(Player $player, bool $shouldTakeFallDamage, int $duration = 1): void {
      unset(self::$shouldTakeFallDamage[$player->getName()]);
      if (!$shouldTakeFallDamage) self::$shouldTakeFallDamage[$player->getName()] = time() + $duration;
    }

    public static function getNoFallDamageDuration(Player $player): int {
      return (self::$shouldTakeFallDamage[$player->getName()] ?? time()) - time();
    }

    public static function increaseNoFallDamageDuration(Player $player, int $duration = 1): void {
      self::$shouldTakeFallDamage[$player->getName()] += $duration;
    }
  }