<?php

declare(strict_types = 1);

namespace internal\bedrock;

use pocketmine\item\Item;
use pocketmine\nbt\TreeRoot;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use function base64_encode;

class ItemFactory {

  /** @return mixed[] */
  public static function jsonSerialize(Item $item) : array {
    $idMeta = StringToIdMeta::parse($item->__toString());
    $id = $idMeta["id"];
    $meta = $idMeta["meta"];
    $data = ["id" => $id];
    if ($meta !== 0) {
      $data["damage"] = $meta;
    }
    if ($item->getCount() !== 1) {
      $data["count"] = $item->getCount();
    }
    if ($item->hasNamedTag()) {
      $data["nbt_b64"] = base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->getNamedTag())));
    }
    return $data;
  }

  /**
  * @param mixed[] $data
  * @throws SavedDataLoadingException
  */
  public static function jsonDeserialize(array $data) : Item {
    $nbt = "";
    if (isset($data["nbt"])) {
      $nbt = $data["nbt"];
    } elseif (isset($data["nbt_hex"])) {
      $nbt = hex2bin($data["nbt_hex"]);
    } elseif (isset($data["nbt_b64"])) {
      $nbt = base64_decode($data["nbt_b64"], true);
    }
    $itemStackData = GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataInt(
      (int) $data["id"],
      (int) ($data["damage"] ?? 0),
      (int) ($data["count"] ?? 1),
      $nbt !== "" ? (new LittleEndianNbtSerializer())->read($nbt)->mustGetCompoundTag() : null
    );
    try {
      return GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemStackData);
    }catch(ItemTypeDeserializeException $e) {
      throw new SavedDataLoadingException($e->getMessage(), 0, $e);
    }
  }
}