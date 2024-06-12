<?php

declare(strict_types = 1);

namespace library\item;

use pocketmine\item\Item;
use pocketmine\nbt\TreeRoot;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\AsyncPool;
use pocketmine\item\StringToItemParser;
use library\item\exception\ItemException;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\data\runtime\RuntimeDataWriter;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\world\format\io\GlobalBlockStateHandlers;

/**
* Class ItemFactory
* @package library\item
*/
final class ItemFactory {

  /** @var Item[] */
  private static array $registeredItems = [];

  public static function init(AsyncPool $asyncPool): void {
    $asyncPool->addWorkerStartHook(function(int $worker) use ($asyncPool): void {
      $asyncPool->submitTaskToWorker(new class extends AsyncTask {
        public function onRun() : void {
          foreach (ItemFactory::getRegisteredItems() as $name => $item) {
            GlobalItemDataHandlers::getDeserializer()->map($item->getTypeId(), fn() => clone $item);
            GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($item->getTypeId()));
            StringToItemParser::getInstance()->register($name, fn() => clone $item);
          }
        }
      }, $worker);
    });
  }

  /**
  * Register a new item.
  * @param Item $item 
  * @return string
  */
  public static function register(Item $item): string {
    $name = strtolower(str_replace(' ', '_', $item->getVanillaName()));
    self::$registeredItems[$name] = $item;
    return $name;
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
  * @return array
  */
  public static function jsonSerialize(Item $item): array {
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
      
      $vanillaName = $data['vanillaName'] ?? null;
      if (isset(self::$registeredItems[$vanillaName])) {
        $item = clone self::$registeredItems[$vanillaName];
      }

      if ($item === null) {
        if ($vanillaName === null) {
          throw new ItemException("No search vanillaName [$vanillaName]");
        }
        $item = StringToItemParser::getInstance()->parse($vanillaName);
      }

      if ($item === null) {
        throw new ItemException("Unexpected item $vanillaName.");
      }

      $item->setCount($data['count'] ?? 1);
      $nbt = $data['nbt'] ?? null;
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
    } catch (\Throwable $e) {
      new \crashdump($e);
      return null;
    }
  }

}