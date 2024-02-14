<?php

declare(strict_types = 1);

namespace libraries\utils;

/**
* Class Path
* @package libraries\utils
*/
final class Path {
  
  /**
  * Path constructor.
  * @param string $dir
  */
  public function __construct(?String $dir) {
    self::load($dir);
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
  public static function getDataFolder(): string {
    try {
      return \Plugin::getInstance()->getDataFolder();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Load a directory within the data folder.
  *
  * @param string $directory
  */
  public static function load(string $directory): void {
    try {
      @mkdir(Path::getDataFolder() . $directory);
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }
}