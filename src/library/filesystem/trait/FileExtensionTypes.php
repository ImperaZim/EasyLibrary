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
  
}