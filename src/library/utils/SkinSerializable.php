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
  * Serializes the Skin object into an associative skin.
  * @param Skin $skin The skin containing the data to deserialize.
  * @return string The string containing the serialized data of the Skin.
  */
  public static function jsonSerialize(Skin $skin): string {
    return json_encode([
      'skinId' => $skin->getSkinId(),
      'skinData' => $skin->getSkinData(),
      'capeData' => $skin->getCapeData(),
      'geometryName' => $skin->getGeometryName(),
      'geometryData' => $skin->getGeometryData(),
    ]);
  }

  /**
  * Deserializes data from an associative array to create a new instance of Skin.
  * @param string $data The array containing the data to deserialize.
  * @return Skin The new instance of Skin created based on the provided data.
  */
  public static function jsonDeserialize(string $data): Skin {
    $data = json_decode($data);
    return new Skin(
      $data['skinId'],
      $data['skinData'],
      $data['capeData'] ?? "",
      $data['geometryName'] ?? "",
      $data['geometryData'] ?? ""
    );
  }
}