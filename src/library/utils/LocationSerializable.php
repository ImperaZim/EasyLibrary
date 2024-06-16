<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\Server;
use pocketmine\entity\Location;
use library\utils\exception\Exception;

/**
* Class LocationSerializable
* @package library\utils
*/
class LocationSerializable implements JsonSerializable {

  /**
  * Deserialize a JSON string to a Location object.
  * @param string $jsonString The JSON string to deserialize.
  * @return Location|null The deserialized object or null on failure.
  */
  public static function jsonDeserialize(string $jsonString): ?Location {
    try {
      $data = json_decode($jsonString, true);
      if (isset($data['x'], $data['y'], $data['z'], $data['yaw'], $data['pitch'], $data['world'])) {
        $world = Server::getInstance()->getWorldManager()->getWorldByName($data['world']);
        if ($world !== null) {
          return new Location((float)$data['x'], (float)$data['y'], (float)$data['z'], $world, (float)$data['yaw'], (float)$data['pitch']);
        }
      }
    } catch (Exception $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Serialize a Location object to a JSON string.
  * @param object $object The object to serialize.
  * @return string The serialized JSON string.
  * @throws Exception If the object is not an instance of Location.
  */
  public static function jsonSerialize(object $object): string {
    if (!$object instanceof Location) {
      throw new Exception("Object must be an instance of Location");
    }
    $data = [
      'x' => $object->getX(),
      'y' => $object->getY(),
      'z' => $object->getZ(),
      'yaw' => $object->getYaw(),
      'pitch' => $object->getPitch(),
      'world' => $object->isValid() ? $object->getWorld()->getDisplayName() : "null"
    ];
    return json_encode($data);
  }

}