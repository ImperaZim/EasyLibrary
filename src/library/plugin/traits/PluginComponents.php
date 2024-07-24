<?php

declare(strict_types = 1);

namespace library\plugin\traits;

use library\filesystem\File;
use library\plugin\PluginToolkit;

/**
* Trait PluginComponents
* @package library\plugin\traits
*/
trait PluginComponents {

  /**
  * Constant for the component type 'network|command|listener'.
  * @var string
  */
  public const NETWORK_COMPONENT = 'network';
  public const COMMAND_COMPONENT = 'command';
  public const LISTENER_COMPONENT = 'listener';
  public const SCHEDULER_COMPONENT = 'scheduler';

  /** @var array */
  private static array $files = [];
  /** @var PluginToolkit */
  private static PluginToolkit $plugin;

  /**
  * Set the File instance with a token.
  * @param string $token
  * @param File $file
  */
  public static function setFile(string $token, File $file): void {
    self::$files[$token] = $file;
  }

  /**
  * Get the File instance.
  * If no token is provided and only one file exists, return that file.
  * @param string|null $token
  * @return File
  * @throws \RuntimeException If the token is not provided and there are multiple files.
  */
  public static function getFile(?string $token = null): File {
    if ($token !== null) {
      if (isset(self::$files[$token])) {
        return self::$files[$token];
      } else {
        throw new \RuntimeException("No file found for token: $token");
      }
    }

    if (count(self::$files) === 1) {
      return reset(self::$files);
    }

    throw new \RuntimeException("Token must be provided when multiple files are set.");
  }

  /**
  * Set the PluginToolkit instance.
  * @param PluginToolkit $plugin
  */
  public static function setPlugin(PluginToolkit $plugin): void {
    self::$plugin = $plugin;
  }

  /**
  * Get the PluginToolkit instance.
  * @return PluginToolkit
  */
  public static function getPlugin(): PluginToolkit {
    return self::$plugin;
  }
}