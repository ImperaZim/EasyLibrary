<?php

declare(strict_types = 1);

namespace library\filesystem\trait;

/**
* Trait FileExtensionTypes
* @package library\filesystem\trait
*/
trait FileExtensionTypes {

  /** YML file type constant. */
  public const TYPE_YML = 'file:yml';

  /** YAML file type constant. */
  public const TYPE_YAML = 'file:yaml';

  /** JSON file type constant. */
  public const TYPE_JSON = 'file:json';

  /** TXT file type constant. */
  public const TYPE_TXT = 'file:txt';

  /** File type to extension mapping */
  private static array $typeToExtension = [
    self::TYPE_YML => 'yml',
    self::TYPE_YAML => 'yaml',
    self::TYPE_JSON => 'json',
    self::TYPE_TXT => 'txt'
  ];

  /**
  * Get the available file types.
  * @return array
  */
  public static function getTypes(): array {
    return array_keys(self::$typeToExtension);
  }

  /**
  * Get the available file extensions without the 'file:' prefix.
  * @return array
  */
  public static function getExtensions(): array {
    return array_values(self::$typeToExtension);
  }

  /**
  * Matches a file extension to its type.
  * @param string $extension
  * @return string|null
  */
  public static function getTypeByExtension(string $extension): ?string {
    $extension = strtolower($extension);
    $type = array_search($extension, self::$typeToExtension, true);
    return $type !== false ? $type : null;
  }

  /**
  * Gets the file extension by type.
  * @param string $type
  * @param bool $withPoint
  * @return string|null
  */
  public static function getExtensionByType(string $type, bool $withPoint = false): ?string {
    $extension = self::$typeToExtension[$type] ?? null;
    return $extension !== null ? ($withPoint ? '.' . $extension : $extension) : null;
  }
  
}