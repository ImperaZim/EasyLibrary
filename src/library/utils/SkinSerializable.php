<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\entity\Skin;

/**
* Class SkinSerializable
* @package library\utils
*/
final class SkinSerializable {

  /**
  * Serializes the Skin object into a JSON string.
  * @param Skin $skin The skin containing the data to serialize.
  * @return string The JSON string containing the serialized data of the Skin.
  */
  public static function jsonSerialize(Skin $skin): string {
    return json_encode([
      'skinId' => $skin->getSkinId(),
      'skinData' => $skin->getSkinData(),
      'capeData' => $skin->getCapeData(),
      'geometryName' => $skin->getGeometryName(),
      'geometryData' => $skin->getGeometryData(),
    ], JSON_THROW_ON_ERROR);
  }

  /**
  * Deserializes data from a JSON string to create a new instance of Skin.
  * @param string $data The JSON string containing the data to deserialize.
  * @return Skin The new instance of Skin created based on the provided data.
  */
  public static function jsonDeserialize(string $data): Skin {
    $decodedData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    return new Skin(
      $decodedData['skinId'],
      $decodedData['skinData'],
      $decodedData['capeData'] ?? "",
      $decodedData['geometryName'] ?? "",
      $decodedData['geometryData'] ?? ""
    );
  }
}