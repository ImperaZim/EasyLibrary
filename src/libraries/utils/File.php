<?php

declare(strict_types = 1);

namespace libraries\utils;

use pocketmine\utils\Config;

/**
* Class File
* @package libraries\utils
*/
final class File {

  private Config $config;

  /**
  * File constructor.
  * @param string $file
  * @param string|null $extension
  */
  public function __construct(
    string $file,
    ?string $extension = '.yml'
  ) {
    try {
      $filePath = Path::getDataFolder() . $file . $extension;
      if (!file_exists($filePath)) {
        \Plugin::getInstance()->saveResource($file . $extension);
      }
      if ($extension !== '.php') {
        $this->config = new Config($filePath);
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Get a nested value from the configuration file.
  *
  * @param string $nested
  * @param mixed $default
  * @return mixed
  */
  public function get(string $nested, mixed $default = null): mixed {
    try {
      return $this->config->getNested($nested, $default);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Set a nested value in the configuration file.
  *
  * @param array $nested
  */
  public function set(array $nested): void {
    try {
      $config = $this->config;
      $config->setNested(array_keys($nested)[0], array_values($nested)[0]);
      $config->save();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Unset a value from the configuration file.
  *
  * @param array $nested
  * @param string $value
  */
  public function unset(array $nested, string $value): void {
    try {
      $config = $this->config;
      if (isset($nested[$value])) {
        $array = $nested;
        unset($array[$value]);
        $config->setAll($array);
        $config->save();
      }
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Set multiple values in the configuration file.
  *
  * @param mixed $nested
  */
  public function setAll(mixed $nested): void {
    try {
      $config = $this->config;
      $config->setAll($nested);
      $config->save();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Load a configuration file.
  *
  * @param string $file
  */
  public static function load(string $file): void {
    try {
      \Plugin::getInstance()->saveResource(Path::getDataFolder() . $file . '.yml');
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Convert the configuration file to an array.
  *
  * @return array
  */
  public function toArray(): array {
    try {
      return $this->config->getAll();
    } catch (\Throwable $e) {
      return [];
    }
  }

  /**
  * Get the Config instance.
  *
  * @return Config
  */
  public function getConfig(): Config {
    try {
      return $this->config;
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}