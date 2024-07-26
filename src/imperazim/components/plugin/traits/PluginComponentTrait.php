<?php

declare(strict_types = 1);

namespace imperazim\components\plugin\traits;

use imperazim\components\filesystem\File;
use imperazim\components\plugin\PluginToolkit;

/**
* Trait PluginComponentsTrait
* @package imperazim\components\plugin\traits
*/
trait PluginComponentsTrait {
  use ComponentTypesTrait;

  /** @var array */
  private static array $files = [];
  /** @var PluginToolkit */
  private static ?PluginToolkit $plugin = null;

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

  /**
  * Call a method on the plugin instance.
  * @param string $method
  * @param array $args
  * @return mixed
  * @throws \BadMethodCallException If the method does not exist on the plugin instance.
  */
  public static function callPluginMethod(string $method, array $args = []) {
    if (method_exists(self::$plugin, $method)) {
      return self::$plugin->$method(...$args);
    }

    throw new \BadMethodCallException("Method $method does not exist on the plugin instance.");
  }
}