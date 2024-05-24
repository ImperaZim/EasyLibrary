<?php

declare(strict_types = 1);

namespace library\metadata;

/**
* Class MetadataCollection
* @package library\metadata
*/
final class MetadataCollection {

  /** @var array */
  private array $metadata;

  /**
  * MetadataCollection constructor.
  * Initializes the metadata as an empty array.
  */
  public function __construct(?array $metadata = []) {
    $this->metadata = $metadata;
  }

  /**
  * Sets a metadata with presset key.
  * @param string $key
  * @param mixed $value
  */
  public static function clone(array $metadata) : MetadataCollection {
    return new MetadataCollection($metadata);
  }

  /**
  * Sets a metadata value associated with a key.
  * @param string $key
  * @param mixed $value
  */
  public function setMetaData(string $key, $value): self {
    $this->metadata[$key] = $value;
    return $this;
  }

  /**
  * Gets a metadata value associated with a key.
  * @param string|null $key
  * @return mixed|null
  */
  public function getData(?string $key = null): mixed {
    if ($key === null) {
      return $this->metadata;
    }
    return $this->metadata[$key] ?? null;
  }

  /**
  * Removes a metadata value associated with a key.
  * @param string $key
  */
  public function removeMetadata(string $key): self {
    unset($this->metadata[$key]);
    return $this;
  }

  /**
  * Clears all metadata.
  */
  public function clearMetadata(): self {
    $this->metadata = [];
    return $this;
  }

  /**
  * Checks if the metadata collections are identical.
  * @param MetadataCollection $collection The collection to compare with.
  * @return bool Returns true if the metadata collections are identical, false otherwise.
  */
  public function equals(MetadataCollection $collection): bool {
    return $this->metadata === $collection->getData();
  }

  /**
  * Returns a string representation of the MetadataCollection.
  * @return string Returns the string representation.
  */
  public function __toString(): string {
    return __CLASS__ . " Metadata: " . json_encode($this->metadata);
  }

}