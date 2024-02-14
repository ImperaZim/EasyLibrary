<?php

declare(strict_types = 1);

namespace libraries\utils;

use pocketmine\plugin\PluginBase;

/**
* Class Path
* @package libraries\utils
*/
final class Path {
  
  /**
  * Path constructor.
  * @param PluginBase $plugin
  * @param string $dir
  */
  public function __construct(private PluginBase $plugin, ?String $dir) {
    self::load($plugin, $dir);
  }
  
  /**
  * Paste files and directories from one location to another.
  *
  * @param string $directory
  * @param string $destinatary
  */
  public static function paste(string $directory, string $destinatary): void {
    try {
      @mkdir($destinatary);
      $dir = opendir($directory);
      while ($file = readdir($dir)) {
        if (($file != '.') && ($file != '..')) {
          if (is_dir($directory . '/' . $file)) {
            $dirFile = $directory . '/' . $file;
            $destinataryFile = $destinatary . '/' . $file;
            Path::paste($dirFile, $destinataryFile);
          } else {
            $dirFile = $directory . '/' . $file;
            $destinataryFile = $destinatary . '/' . $file;
            copy($dirFile, $destinataryFile);
          }
        }
      }
      closedir($dir);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Get the data folder path.
  *
  * @return string
  */
  public static function getDataFolder(PluginBase $plugin): string {
    try {
      return $plugin->getDataFolder();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Load a directory within the data folder.
  *
  * @param PluginBase $plugin
  * @param string $directory
  */
  public static function load(PluginBase $plugin, string $directory): void {
    try {
      @mkdir(Path::getDataFolder($plugin) . $directory);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}