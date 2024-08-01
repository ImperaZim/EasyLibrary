<?php

declare(strict_types = 1);

namespace imperazim\components\item;

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
* Trait ItemFactory
* @package imperazim\components\item
*/
trait ItemFactory {

  /** @var Item[] */
  private static array $registeredItems = [];
  /** @var Block[] */
  private static array $registeredBlocks = [];

  private int $nextBlockId = BlockTypeIds::FIRST_UNUSED_BLOCK_ID + 1;

  /**
  * Register a new item.
  * @param Item $item
  * @param int $runtimeId
  * @param bool $force
  * @param string $namespace
  * @param \Closure|null $serializeCallback
  * @param \Closure|null $deserializeCallback
  * @return mixed
  */
  public static function registerItem(
    Item $item,
    int $runtimeId,
    bool $force = false,
    string $namespace = "",
    ?\Closure $serializeCallback = null,
    ?\Closure $deserializeCallback = null
  ): mixed {
    self::validateCallbacks($serializeCallback, $deserializeCallback);

    if (self::isItemRegistered($item) && !$force) {
      throw new AssumptionFailedError("Item {$item->getTypeId()} is already registered");
    }

    self::$registeredItems[$item->getTypeId()] = $item;
    self::overrideStringToItemParser($item);
    self::registerSerializer($item, $namespace, $serializeCallback);
    self::registerDeserializer($item, $namespace, $deserializeCallback);
    self::updateTypeDictionary($item, $runtimeId, $namespace);
    return self::$registeredItems[$item->getTypeId()];
  }

  /**
  * Validates the provided callbacks.
  * @param \Closure|null $serializeCallback
  * @param \Closure|null $deserializeCallback
  */
  private static function validateCallbacks(?\Closure $serializeCallback, ?\Closure $deserializeCallback): void {
    if ($serializeCallback !== null) {
      Utils::validateCallableSignature(static function (Item $item): SavedItemData {}, $serializeCallback);
    }

    if ($deserializeCallback !== null) {
      Utils::validateCallableSignature(static function (SavedItemData $data): Item {}, $deserializeCallback);
    }
  }

  /**
  * Checks if the item is already registered.
  * @param Item $item
  * @return bool
  */
  private static function isItemRegistered(Item $item): bool {
    return isset(self::$registeredItems[$item->getTypeId()]);
  }

  /**
  * Overrides the string to item parser.
  * @param Item $item
  */
  private static function overrideStringToItemParser(Item $item): void {
    StringToItemParser::getInstance()->override($item->getName(), static fn() => clone $item);
  }

  /**
  * Registers the serializer for the item.
  * @param Item $item
  * @param string $namespace
  * @param \Closure|null $serializeCallback
  */
  private static function registerSerializer(Item $item, string $namespace, ?\Closure $serializeCallback): void {
    $serializer = GlobalItemDataHandlers::getSerializer();
    $namespace = self::generateNamespace($item, $namespace);

    (function () use ($item, $serializeCallback, $namespace): void {
      $this->itemSerializers[$item->getTypeId()] = $serializeCallback ?? static fn() => new SavedItemData($namespace);
    })->call($serializer);
  }

  /**
  * Registers the deserializer for the item.
  * @param Item $item
  * @param string $namespace
  * @param \Closure|null $deserializeCallback
  */
  private static function registerDeserializer(Item $item, string $namespace, ?\Closure $deserializeCallback): void {
    $deserializer = GlobalItemDataHandlers::getDeserializer();
    $namespace = self::generateNamespace($item, $namespace);

    (function () use ($item, $deserializeCallback, $namespace): void {
      if (isset($this->deserializers[$item->getName()])) {
        unset($this->deserializers[$item->getName()]);
      }
      $this->map($namespace, $deserializeCallback ?? static fn(SavedItemData $_) => clone $item);
    })->call($deserializer);
  }

  /**
  * Updates the type dictionary with the new item.
  * @param Item $item
  * @param int $runtimeId
  * @param string $namespace
  */
  private static function updateTypeDictionary(Item $item,
    int $runtimeId,
    string $namespace): void {
    $dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
    $namespace = self::generateNamespace($item,
      $namespace);

    (function () use ($item, $runtimeId, $namespace): void {
      $this->stringToIntMap[$namespace] = $runtimeId;
      $this->intToStringIdMap[$runtimeId] = $namespace;
      $this->itemTypes[] = new ItemTypeEntry($namespace, $runtimeId, true);
    })->call($dictionary);
  }

  /**
  * Generates a namespace for the item if not provided.
  * @param Item $item
  * @param string $namespace
  * @return string
  */
  private static function generateNamespace(Item $item,
    string $namespace): string {
    return $namespace === "" ? "minecraft:" . strtolower(str_replace(" ", "_", $item->getName())) : $namespace;
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

}