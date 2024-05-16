<?php

declare(strict_types = 1);

namespace metadata;

/**
* Class MetadataSerializer
* @package metadata
*/
class MetadataSerializer {

  /**
  * Serialize metadata to a JSON-formatted string.
  * @param MetadataCollection $collection
  * @return string
  */
  public static function serialize(MetadataCollection $collection): string {
    $jsonString = json_encode($collection->getMetadata());
    return $jsonString ?: '';
  }

  /**
  * Deserialize a JSON-formatted string to a MetadataCollection.
  * @param string $jsonString
  * @return MetadataCollection
  */
  public static function deserialize(string $data): MetadataCollection {
    $metadataCollection = new MetadataCollection();
    foreach (json_decode(base64_decode($data), true) as $key => $value) {
      $metadataCollection->setMetadata($key, $value);
    }
    return $metadataCollection;
  }
}