<?php

declare(strict_types = 1);

namespace utils;

use pocketmine\item\Item;
use pocketmine\nbt\TreeRoot;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\world\format\io\GlobalItemDataHandlers;

/**
* Class ItemSerializer
* @package utils
*/
final class ItemSerializer {

  /**
  * Returns an array of item stack properties that can be serialized to json.
  * @return mixed[]
  */
  final public static function encode(Item $item): ?array {
    if ($item == VanillaItems::AIR()) {
      return null;
    }
    $data = [
      'id' => $item->getTypeId()
    ];
    if ($item->getStateId() !== 0) {
      $data['state_id'] = $item->getStateId();
    }
    if ($item->getCount() !== 1) {
      $data['count'] = $item->getCount();
    }
    if ($item->hasNamedTag()) {
      $data['nbt_b64'] = base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->getNamedTag())));
    }
    return $data;
  }

  /**
  * Deserializes item JSON data produced by json_encode()ing Item instances in older versions of PocketMine-MP.
  * @param mixed[] $data
  * @throws SavedDataLoadingException
  */
  final public static function decode(array $data): Item {
    $nbt = '';
    if (isset($data['nbt_b64'])) {
      $nbt = base64_decode($data['nbt_b64'], true);
    }
    $itemStackData = GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataInt(
      (int)$data['id'],
      (int)($data['state_id'] ?? 0),
      (int)($data['count'] ?? 1),
      $nbt !== '' ? (new LittleEndianNbtSerializer())->read($nbt)->mustGetCompoundTag() : null
    );

    try {
      return GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemStackData);
    } catch (ItemTypeDeserializeException $e) {
      throw new SavedDataLoadingException($e->getMessage(), 0, $e);
    }
  }

}