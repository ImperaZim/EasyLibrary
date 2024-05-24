<?php

declare(strict_types = 1);

namespace library\metadata;

use library\utils\File;

/**
* Class Metadata
* @package library\metadata
*/
class Metadata {

  /**
  * Metadata constructor.
  * @param File $config
  */
  public function __construct(private File $config) {
    /** TODO null */
  }

  /**
  * Set metadata for the config.
  * @param String|MetadataCollection $collection
  * @param mixed $value
  * @return static
  */
  public function set(String|MetadataCollection $collection, mixed $value = null): static {
    if ($collection instanceof MetadataCollection) {
      $value = base64_encode($collection->toString());
      $this->config->set(['metadata' => $value]);
    } else {
      $this->update($collection, $value);
    }
    return $this;
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
  * @return static
  */
  public function update(string $key, mixed $value): static {
    $this->set($this->getCollection()->setMetaData($key, $value)); 
    return $this;
  }
}