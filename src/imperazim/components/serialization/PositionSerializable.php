<?php

declare(strict_types = 1);

namespace imperazim\components\serialization;

use pocketmine\Server;
use pocketmine\world\Position;
use imperazim\components\exception\Exception;

/**
* Class PositionSerializable
* @package imperazim\components\serialization
*/
class PositionSerializable implements JsonSerializable {

  /**
  * Deserialize a JSON string to a Position object.
  * @param string $jsonString The JSON string to deserialize.
  * @return Position|null The deserialized object or null on failure.
  */
  public static function jsonDeserialize(string $jsonString): ?Position {
    try {
      $data = json_decode($jsonString, true);
      if (isset($data['x'], $data['y'], $data['z'], $data['world'])) {
        $world = Server::getInstance()->getWorldManager()->getWorldByName($data['world']);
        return new Position((float)$data['x'], (float)$data['y'], (float)$data['z'], $world);
      }
    } catch (Exception $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Serialize a Position object to a JSON string.
  * @param object $object The object to serialize.
  * @return string The serialized JSON string.
  * @throws Exception If the object is not an instance of Position.
  */
  public static function jsonSerialize(object $object): string {
    if (!$object instanceof Position) {
      throw new Exception("Object must be an instance of Position");
    }
    $data = [
      'x' => $object->getX(),
      'y' => $object->getY(),
      'z' => $object->getZ(),
      'world' => $object->isValid() ? $object->getWorld()->getDisplayName() : "null"
    ];
    return json_encode($data);
  }

}