<?php

declare(strict_types = 1);

namespace metadata;

use libraries\utils\File;

/**
* Class Metadata
* @package metadata
*/
class Metadata {
  
  /**
  * Metadata constructor.
  * @param File $config
  */
  public function __construct(private File $config) {}

  /**
  * Set metadata for the config.
  * @param String|MetadataCollection $collection
  * @param mixed $value
  */
  public function set(String|MetadataCollection $collection, mixed $value = null): void {
    if ($collection instanceof MetadataCollection) {
      $value = base64_encode($collection->toString());
      $this->config->set(['metadata' => $value]);
    } else {
      $this->update($collection, $value);
    }
  }

  /**
  * Get a specific metadata value or a default if not found.
  * @param string|null $key
  * @param mixed $default
  * @return mixed
  */
  public function get(string $key = null, mixed $default = null): mixed {
    $collection = $this->getCollection();
    return $key !== null ? $collection->getData($key) : $default;
  }

  /**
  * Get metadata collection associated with the config.
  * @return MetadataCollection
  */
  public function getCollection(): MetadataCollection {
    $data = $this->config->get('metadata', "{}");
    return MetadataSerializer::deserialize($data);
  }

  /**
  * Update a specific metadata value.
  * @param string $key
  * @param mixed $value
  */
  public function update(string $key, mixed $value): void {
    $this->set($this->getCollection()->setMetaData($key, $value));
  }
}