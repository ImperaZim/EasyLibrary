<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\math\Vector3;
use library\utils\exception\Exception;

/**
* Class Vector3Serializable
* @package library\utils
*/
class Vector3Serializable implements JsonSerializable {

  /**
  * Deserialize a JSON string to a Vector3 object.
  * @param string $jsonString The JSON string to deserialize.
  * @return Vector3|null The deserialized object or null on failure.
  */
  public static function jsonDeserialize(string $jsonString): ?Vector3 {
    try {
      $data = json_decode($jsonString, true);
      if (isset($data['x'], $data['y'], $data['z'])) {
        return new Vector3((float)$data['x'], (float)$data['y'], (float)$data['z']);
      }
    } catch (Exception $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Serialize a Vector3 object to a JSON string.
  * @param object $object The object to serialize.
  * @return string The serialized JSON string.
  * @throws Exception If the object is not an instance of Vector3.
  */
  public static function jsonSerialize(object $object): string {
    if (!$object instanceof Vector3) {
      throw new Exception("Object must be an instance of Vector3");
    }
    $data = [
      'x' => $object->getX(),
      'y' => $object->getY(),
      'z' => $object->getZ()
    ];
    return json_encode($data);
  }
  
}