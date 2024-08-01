<?php

declare(strict_types = 1);

namespace imperazim\components\serialization;

/**
* Interface JsonSerializable
* @package imperazim\components\serialization
*/
interface JsonSerializable {
  
  /**
  * Deserialize a JSON string to an object.
  * @param string $jsonString The JSON string to deserialize.
  * @return object|null The deserialized object or null on failure.
  */
  public static function jsonDeserialize(string $jsonString): ?object;

  /**
  * Serialize the object to a JSON string.
  * @param object $object The object to serialize.
  * @return string The serialized JSON string.
  */
  public static function jsonSerialize(object $object): string;
  
}