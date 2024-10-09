<?php

declare(strict_types = 1);

namespace imperazim\components\serialization;

use imperazim\components\exception\Exception;

use pocketmine\item\Item;
use pocketmine\nbt\TreeRoot;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\LittleEndianNbtSerializer;

use function json_decode;
use function str_replace;
use function strtolower;

/**
* Class ItemSerializable
* @package imperazim\components\serialization
*/
final class ItemSerializable {

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
        throw new Exception("Error decoding JSON: " . json_last_error_msg());
      }

      $vanillaName = $data->vanillaName ?? null;
      if ($vanillaName === null) {
        throw new Exception("No vanillaName found in data.");
      }
      $item = StringToItemParser::getInstance()->parse($vanillaName);

      if ($item === null) {
        throw new Exception("Failed to create item from vanillaName: $vanillaName.");
      }

      $item->setCount($data->count ?? 1);
      $nbt = $data->nbt ?? null;
      if ($nbt !== null) {
        $nbtData = base64_decode($nbt, true);
        if ($nbtData === false) {
          throw new Exception("Invalid base64 NBT data");
        }
        $nbtSerializer = new LittleEndianNbtSerializer();
        $nbt = $nbtSerializer->read($nbtData)->getTag();
        if (!$nbt instanceof CompoundTag) {
          throw new Exception("Invalid NBT data");
        }
        $item->setNamedTag($nbt);
      }
      return $item;
    } catch (Exception $e) {
      new \crashdump($e);
      return null;
    }
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
    } catch (Exception $e) {
      new \crashdump($e);
      return null;
    }
  }
}