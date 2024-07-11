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
  * @return array The array containing the serialized data of the Skin.
  */
  public static function jsonSerialize(Skin $skin): array {
    return [
      'skinId' => $skin->getSkinId(),
      'skinData' => base64_encode($skin->getSkinData()),
      'capeData' => $skin->getCapeData(),
      'geometryName' => $skin->getGeometryName(),
      'geometryData' => $skin->getGeometryData(),
    ];
  }

  /**
  * Deserializes data from an associative array to create a new instance of Skin.
  * @param array $data The array containing the data to deserialize.
  * @return Skin The new instance of Skin created based on the provided data.
  */
  public static function jsonDeserialize(array $data): Skin {
    return new Skin(
      $data['skinId'],
      base64_decode($data['skinData']),
      $data['capeData'] ?? "",
      $data['geometryName'] ?? "",
      $data['geometryData'] ?? ""
    );
  }
}