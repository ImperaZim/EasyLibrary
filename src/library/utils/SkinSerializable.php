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
  * Serializes the Skin object into an associative array.
  * @return array The array containing the serialized data of the Skin.
  */
  public function serialize(): array {
    return [
      'skinId' => $this->skinId,
      'skinData' => $this->skinData,
      'capeData' => $this->capeData,
      'geometryName' => $this->geometryName,
      'geometryData' => $this->geometryData,
    ];
  }

  /**
  * Deserializes data from an associative array to create a new instance of Skin.
  * @param array $data The array containing the data to deserialize.
  * @return Skin The new instance of Skin created based on the provided data.
  */
  public static function deserialize(array $data): Skin {
    return new Skin(
      $data['skinId'],
      $data['skinData'],
      $data['capeData'] ?? "",
      $data['geometryName'] ?? "",
      $data['geometryData'] ?? ""
    );
  }
}