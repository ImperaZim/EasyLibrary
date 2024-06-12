<?php

declare(strict_types = 1);

namespace library\utils;

use pocketmine\plugin\Plugin;

/**
* Class Config
* @package library\utils
*/
final class Config extends \pocketmine\utils\Config {
  
  /**
  * File constructor.
  * @param Plugin $plugin
  * @param string $file
  * @param int $type
  * @param array $default
  */
  public function __construct(
    private Plugin $plugin,
    public string $file, 
    public int $type = self::DETECT, 
    public array $default = [])
  {
    parent::__construct($file, $type, $default);
  }
  
  /**
   * Gets the plugin instance
   * @return Plugin
   */
  public function getPlugin(): Plugin {
    return $this->plugin;
  }
  
}