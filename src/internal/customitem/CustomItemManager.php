<?php

declare(strict_types = 1);

namespace internal\customitem;

use internal\customitem\item\CustomArmorItem;
use internal\customitem\item\CustomDurableItem;
use internal\customitem\item\CustomFoodItem;
use internal\customitem\item\CustomItem;
use internal\customitem\item\CustomItemTrait;
use internal\customitem\item\CustomToolItem;
use internal\customitem\item\properties\CustomItemProperties;
use alvin0319\libItemRegistrar\libItemRegistrar;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionProperty;
use Throwable;
use function str_replace;
use function strtolower;

final class CustomItemManager {
  use SingletonTrait;

  /** @var Item[] */
  private array $registered = [];

  private ItemComponentPacket $packet;

  private ReflectionProperty $itemTypeMap;

  /** @var ItemComponentPacketEntry[] */
  private array $packetEntries = [];
  /** @var ItemTypeEntry[] */
  private array $itemTypeEntries = [];

  public function __construct() {}

  public function getItems() : array {
    return $this->registered;
  }

  public function isCustomItem(Item $item) : bool {
    foreach ($this->registered as $other) {
      if ($item->equals($other, false, false)) {
        return true;
      }
    }
    return false;
  }

  /**
  * @param CustomItemTrait|Item $item
  */
  public function registerItem($item) : void {
    try {
      $runtimeId = $item->getProperties()->getRuntimeId();

      $this->itemTypeEntries[] = new ItemTypeEntry($item->getProperties()->getNamespace(), $runtimeId, true);

      $this->packetEntries[] = new ItemComponentPacketEntry($item->getProperties()->getNamespace(), new CacheableNbt($item->getProperties()->getNbt()));

      $this->registered[] = $item;

      $new = clone $item;

      $this->internalRegisterItem($new, $runtimeId, true, $item->getProperties()->getNamespace());
    }catch(Throwable $e) {
      throw new \InvalidArgumentException("Failed to register item: " . $e->getMessage(), $e->getLine(), $e);
    }
  }

  private function refresh() : void {
    $this->packet = ItemComponentPacket::create($this->packetEntries);
  }

  public function getPacket() : ItemComponentPacket {
    return clone $this->packet;
  }

  public function registerDefaultItems(array $data) : void {
    foreach ($data as $name => $itemData) {
      var_dump(self::getItem($name, $itemData)->getVanillaName());
      $this->registerItem(self::getItem($name, $itemData));
    }
    $this->refresh();
  }

  public static function getItem(string $name, array $data) : Item {
    $prop = new CustomItemProperties($name, $data);
    if ($prop->isDurable()) {
      return new CustomDurableItem($name, $prop);
    }
    if ($prop->isFood()) {
      return new CustomFoodItem($name, $prop);
    }
    if ($prop->isArmor()) {
      return new CustomArmorItem($name, $prop);
    }
    if ($prop->isTool()) {
      return new CustomToolItem($name, $prop);
    }
    return new CustomItem($name, $prop);
  }

  /**
  * @param Item          $item the Item to register
  * @param int           $runtimeId the runtime id that will be used by the server to send the item to the player.
  * This usually can be found using BDS, or included in {@link \pocketmine\BEDROCK_DATA_PATH/required_item_list.json}. for custom items, you should generate this manually.
  * @param bool          $force
  * @param string        $namespace the item's namespace. This usually can be found in {@link ItemTypeNames}.
  * @param \Closure|null $serializeCallback the callback that will be used to serialize the item.
  * @param \Closure|null $deserializeCallback the callback that will be used to deserialize the item.
  *
  * @return void
  * @see ItemTypeDictionaryFromDataHelper
  * @see libItemRegistrar::getRuntimeIdByName()
  */
  public function internalRegisterItem(Item $item, int $runtimeId, bool $force = false, string $namespace = "", ?\Closure $serializeCallback = null, ?\Closure $deserializeCallback = null) : void {
    if ($serializeCallback !== null) {
      /** @phpstan-ignore-next-line */
      Utils::validateCallableSignature(static function(Item $item) : SavedItemData {}, $serializeCallback);
    }
    if ($deserializeCallback !== null) {
      Utils::validateCallableSignature(static function(SavedItemData $data) : Item {}, $deserializeCallback);
    }

    StringToItemParser::getInstance()->override($item->getName(), static fn() => clone $item);
    $serializer = GlobalItemDataHandlers::getSerializer();
    $deserializer = GlobalItemDataHandlers::getDeserializer();

    $namespace = $namespace === "" ? "minecraft:" . strtolower(str_replace(" ", "_", $item->getName())) : $namespace;

    // TODO: Closure hack to access ItemSerializer
    // ItemSerializer throws an Exception when we try to register a pre-existing item
    (function() use ($item, $serializeCallback, $namespace) : void {
      $this->itemSerializers[$item->getTypeId()] = $serializeCallback !== null ? $serializeCallback : static fn() => new SavedItemData($namespace);
    })->call($serializer);
    // TODO: Closure hack to access ItemDeserializer
    // ItemDeserializer throws an Exception when we try to register a pre-existing item
    (function() use ($item, $deserializeCallback, $namespace) : void {
      if (isset($this->deserializers[$item->getName()])) {
        unset($this->deserializers[$item->getName()]);
      }
      $this->map($namespace, $deserializeCallback !== null ? $deserializeCallback : static fn(SavedItemData $_) => clone $item);
    })->call($deserializer);

    $dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
    (function() use ($item, $runtimeId, $namespace) : void {
      $this->stringToIntMap[$namespace] = $runtimeId;
      $this->intToStringIdMap[$runtimeId] = $namespace;
      $this->itemTypes[] = new ItemTypeEntry($namespace, $runtimeId, true);
    })->call($dictionary);
  }
}