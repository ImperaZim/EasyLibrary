<?php

declare(strict_types = 1);

namespace internal\libform\elements;

/**
* Class Image
* @param internal\libform\elements
*/
final class Image implements \JsonSerializable {

  /**
  * Private constructor to prevent direct instantiation.
  * @param string $data The data of the image.
  * @param string $type The type of the image.
  */
  private function __construct(
    public /*readonly*/ string $data, 
    public /*readonly*/ string $type
  ) {}

  /**
  * Creates an Image instance with a URL.
  * @param string $data The URL of the image.
  * @return self Returns an Image instance.
  */
  public static function url(string $data): self {
    return new self($data, "url");
  }

  /**
  * Creates an Image instance with a file path.
  * @param string $data The file path of the image.
  * @return self Returns an Image instance.
  */
  public static function path(string $data): self {
    return new self($data, "path");
  }

  /**
  * Returns a null value indicating no image.
  * @return null Returns null to indicate no image.
  */
  public static function null(): null {
    return null;
  }

  /**
  * Serializes the image data to an array for JSON encoding.
  * @return array<string, mixed> The serialized data of the image.
  */
  public function jsonSerialize(): array {
    return [
      "type" => $this->type,
      "data" => $this->data,
    ];
  }
}