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
  * Ensure data is UTF-8 encoded.
  * @param string $data The data to ensure encoding.
  * @return string The UTF-8 encoded data.
  */
  private static function ensureUtf8(string $data): string {
    return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
  }

  /**
  * Serializes the Skin object into a JSON string.
  * @param Skin $skin The skin containing the data to serialize.
  * @return string The JSON string containing the serialized data of the Skin.
  */
  public static function jsonSerialize(Skin $skin): string {
    return json_encode([
      'skinId' => self::ensureUtf8($skin->getSkinId()),
      'skinData' => self::ensureUtf8($skin->getSkinData()),
      'capeData' => self::ensureUtf8($skin->getCapeData()),
      'geometryName' => self::ensureUtf8($skin->getGeometryName()),
      'geometryData' => self::ensureUtf8($skin->getGeometryData()),
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