<?php

declare(strict_types = 1);

namespace library\item;

use library\item\exception\ItemException;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\utils\Utils;
use pocketmine\nbt\TreeRoot;
use pocketmine\item\ItemTypeIds;
use pocketmine\block\BlockTypeIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\AsyncPool;
use Symfony\Component\Filesystem\Path;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\data\runtime\RuntimeDataWriter;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\network\mcpe\convert\ItemTypeDictionaryFromDataHelper;
use function file_get_contents;
use function json_decode;
use function str_replace;
use function strtolower;

/**
* Class ItemFactory
* @package library\item
*/
final class ItemFactory {

  /** @var Item[] */
  private static array $registeredItems = [];
  /** @var Block[] */
  private static array $registeredBlocks = [];

  private int $nextBlockId = BlockTypeIds::FIRST_UNUSED_BLOCK_ID + 1;

  /**
  * Initializes the ItemFactory by registering item handlers in an asynchronous pool.
  * @throws ItemException If an error occurs during initialization.
  */
  public static function init(): void {
    /**
    * TODO: empty
    */
  }

  /**
  * Register a new item.
  * @param Item $item
  * @param int $runtimeId
  * @param bool $force
  * @param string $namespace
  * @param \Closure|null $serializeCallback
  * @param \Closure|null $deserializeCallback
  * @return void
  */
  public static function registerItem(Item $item, int $runtimeId, bool $force = false, string $namespace = "", ?\Closure $serializeCallback = null, ?\Closure $deserializeCallback = null) : void {
    if ($serializeCallback !== null) {
      Utils::validateCallableSignature(static function(Item $item) : SavedItemData {}, $serializeCallback);
    }
    if ($deserializeCallback !== null) {
      Utils::validateCallableSignature(static function(SavedItemData $data) : Item {}, $deserializeCallback);
    }
    if (isset(self::$registeredItems[$item->getTypeId()]) && !$force) {
      throw new AssumptionFailedError("Item {$item->getTypeId()} is already registered");
    }
    self::$registeredItems[$item->getTypeId()] = $item;

    StringToItemParser::getInstance()->override($item->getName(), static fn() => clone $item);
    $serializer = GlobalItemDataHandlers::getSerializer();
    $deserializer = GlobalItemDataHandlers::getDeserializer();

    $namespace = $namespace === "" ? "minecraft:" . strtolower(str_replace(" ", "_", $item->getName())) : $namespace;

    (function() use ($item, $serializeCallback, $namespace) : void {
      $this->itemSerializers[$item->getTypeId()] = $serializeCallback !== null ? $serializeCallback : static fn() => new SavedItemData($namespace);
    })->call($serializer);
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

  /**
  * Returns the runtime id of given item name. (only for vanilla items)
  * @param string $name
  * @return int|null
  */
  public static function getRuntimeIdByName(string $name) : ?int {
    static $mappedJson = [];
    if ($mappedJson === []) {
      $mappedJson = [];
      $data = json_decode(file_get_contents(Path::join(BedrockDataFiles::REQUIRED_ITEM_LIST_JSON)), true);
      foreach ($data as $key => $value) {
        $mappedJson[str_replace("minecraft:", "", $key)] = $value;
      }
    }
    $name = str_replace(" ", "_", strtolower($name));
    return $mappedJson[$name]["runtime_id"] ?? null;
  }

  /**
  * Gets all registered items.
  * @return Item[]
  */
  public static function getRegisteredItems(): array {
    return self::$registeredItems;
  }

  /**
  * Gets the serialized item.
  * @param Item $item
  * @return string|null
  */
  public static function jsonSerialize(Item $item): ?string {
    try {
      $data = [
        "vanillaName" => strtolower(str_replace(' ', '_', $item->getVanillaName()))
      ];
      if ($item->getCount() !== 1) {
        $data["count"] = $item->getCount();
      }
      if ($item->hasNamedTag()) {
        $nbtSerializer = new LittleEndianNbtSerializer();
        $data["nbt"] = base64_encode($nbtSerializer->write(new TreeRoot($item->getNamedTag())));
      }
      return json_encode($data);
    } catch (ItemException $e) {
      new \crashdump($e);
      return null;
    }
  }

  /**
  * Gets an item according to the passed serialize.
  * @param string $data
  * @return Item|null
  */
  public static function jsonDeserialize(string $data): ?Item {
    try {
      $item = null;
      $data = json_decode($data);
      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new ItemException("Error decoding JSON: " . json_last_error_msg());
      }

      $vanillaName = $data->vanillaName ?? null;
      /*
      if (isset(self::$registeredItems[$vanillaName])) {
        $item = clone self::$registeredItems[$vanillaName];
      }
      */

      // if ($item === null) {
      if ($vanillaName === null) {
        throw new ItemException("No vanillaName found in data.");
      }
      $item = StringToItemParser::getInstance()->parse($vanillaName);
      // }

      if ($item === null) {
        throw new ItemException("Failed to create item from vanillaName: $vanillaName.");
      }

      $item->setCount($data->count ?? 1);
      $nbt = $data->nbt ?? null;
      if ($nbt !== null) {
        $nbtData = base64_decode($nbt, true);
        if ($nbtData === false) {
          throw new ItemException("Invalid base64 NBT data");
        }
        $nbtSerializer = new LittleEndianNbtSerializer();
        $nbt = $nbtSerializer->read($nbtData)->getTag();
        if (!$nbt instanceof CompoundTag) {
          throw new ItemException("Invalid NBT data");
        }
        $item->setNamedTag($nbt);
      }
      return $item;
    } catch (ItemException $e) {
      new \crashdump($e);
      return null;
    }
  }
}