<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\entity\Location;

/**
* Class StringToLocationParse 
* @package utils
*/
final class StringToLocationParse {

  /**
  * Encodes a Location object to a JSON string.
  * @param Position $location The Location object to encode.
  * @return string|null The encoded JSON string, or null if encoding fails.
  */
  public static function encode(Position $location): ?string {
    $encoded_location = [
      'x' => $location->getX(),
      'y' => $location->getY(),
      'z' => $location->getZ(),
      'yaw' => $location->getYaw(),
      'pitch' => $location->getPitch(),
      'world' => ($location->isValid() ? $location->getWorld()->getDisplayName() : "null")
    ];
    return json_encode($encoded_location);
  }

  /**
  * Decodes a JSON string to a Location object.
  * @param string $json_string The JSON string containing the location data.
  * @return Location|null The decoded Location object, or null if decoding fails.
  */
  public static function decode(string $json_string): ?Location {
    $decoded_location = json_decode($json_string, true);
    if ($decoded_location !== null && isset($decoded_location['x'], $decoded_location['y'], $decoded_location['z'], $decoded_location['yaw'], $decoded_location['pitch'], $decoded_location['world'])) {
      $world = Server::getInstance()->getWorldManager()->getWorldByName($decoded_location['world']);
      if ($world !== null) {
        $location = new Location($decoded_location['x'], $decoded_location['y'], $decoded_location['z'], $world, $decoded_location['yaw'], $decoded_location['pitch']);
        return $location;
      }
    }
    return null;
  }
}