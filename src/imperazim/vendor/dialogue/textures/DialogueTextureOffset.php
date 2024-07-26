<?php

declare(strict_types = 1);

namespace imperazim\vendor\dialogue\textures;

use JsonSerializable;

/**
* Class DialogueTextureOffset
* @package imperazim\vendor\dialogue\textures
*/
final class DialogueTextureOffset implements JsonSerializable {

  /**
  * Returns the default picker offset.
  * @return self The default picker offset.
  */
  public static function defaultPicker() : self {
    return new self(0, 0, 0, 0, 0, 0);
  }

  /**
  * Returns the default portrait offset.
  * @return self The default portrait offset.
  */
  public static function defaultPortrait() : self {
    return new self(1, 1, 1, 0, 0, 0);
  }

  /**
  * Returns the default player portrait offset.
  * @return self The default player portrait offset.
  */
  public static function defaultPlayerPortrait() : self {
    $parent = self::defaultPortrait();
    return new self($parent->scale_x, $parent->scale_y, $parent->scale_z, $parent->translate_x, -50, $parent->translate_z);
  }

  /**
  * DialogueTextureOffset constructor.
  * @param int|float $scale_x The x scale.
  * @param int|float $scale_y The y scale.
  * @param int|float $scale_z The z scale.
  * @param int|float $translate_x The x translation.
  * @param int|float $translate_y The y translation.
  * @param int|float $translate_z The z translation.
  */
  public function __construct(
    readonly public int|float $scale_x,
    readonly public int|float $scale_y,
    readonly public int|float $scale_z,
    readonly public int|float $translate_x,
    readonly public int|float $translate_y,
    readonly public int|float $translate_z
  ) {}

  /**
  * Serializes the offset to a JSON-compatible array.
  * @return array{scale: array{int|float, int|float, int|float}, translate: array{int|float, int|float, int|float}}
  * The serialized offset.
  */
  public function jsonSerialize() : array {
    return [
      "scale" => [$this->scale_x,
        $this->scale_y,
        $this->scale_z],
      "translate" => [$this->translate_x,
        $this->translate_y,
        $this->translate_z]
    ];
  }
}